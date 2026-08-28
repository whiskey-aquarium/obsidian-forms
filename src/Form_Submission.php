<?php

namespace Obsidian_Forms;

use Obsidian_Forms\Models\Form;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Validates, stores, and optionally emails public form submissions.
 */
final class Form_Submission {
	/**
	 * Registers public and authenticated submission handlers.
	 *
	 * @return void
	 */
	public function initialize(): void {
		add_action( 'admin_post_obsidian_forms_submit', [ $this, 'handle' ] );
		add_action( 'admin_post_nopriv_obsidian_forms_submit', [ $this, 'handle' ] );
	}

	/**
	 * Handles a standard POST submission and redirects to a safe local URL.
	 *
	 * @return never
	 */
	public function handle() {
		$form_id  = absint( $_POST['obsidian_form_id'] ?? 0 );
		$redirect = wp_validate_redirect(
			esc_url_raw( wp_unslash( $_POST['obsidian_form_redirect'] ?? '' ) ),
			home_url( '/' )
		);

		if (
			'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) ||
			! $form_id ||
			! isset( $_POST['obsidian_form_nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['obsidian_form_nonce'] ) ), 'obsidian_form_submit_' . $form_id )
		) {
			$this->redirect( $redirect, 'invalid' );
		}

		// Honeypot submissions appear successful but are neither stored nor mailed.
		if ( ! empty( $_POST['obsidian_form_website'] ) ) {
			$this->redirect( $redirect, 'success' );
		}

		$result = $this->process_submission( $form_id, wp_unslash( $_POST ) );

		if ( is_wp_error( $result ) ) {
			$status = 'validation_failed' === $result->get_error_code() ? 'validation' : $result->get_error_code();
			$this->redirect( $redirect, sanitize_key( $status ) );
		}

		$form          = new Form( $form_id );
		$custom_target = $form->get_settings()['redirectUrl'] ?? '';

		if ( $custom_target ) {
			$redirect = wp_validate_redirect( esc_url_raw( $custom_target ), $redirect );
		}

		$this->redirect( $redirect, 'success' );
	}

	/**
	 * Processes a submission using the mainline field models.
	 *
	 * @param int   $form_id Source form ID.
	 * @param array $data    Unslashed request data.
	 * @return int|\WP_Error Entry ID or error.
	 */
	public function process_submission( int $form_id, array $data ) {
		$form_post = get_post( $form_id );

		if ( ! $form_post instanceof \WP_Post || 'obsidian_form' !== $form_post->post_type || 'publish' !== $form_post->post_status ) {
			return new \WP_Error( 'invalid', __( 'Invalid form.', 'obsidian-forms' ) );
		}

		do_action( 'obsidian_forms_before_submission', $form_id, $data );

		if ( ! $this->check_rate_limit( $form_id ) ) {
			return new \WP_Error( 'rate_limit', __( 'Too many submissions. Please try again later.', 'obsidian-forms' ) );
		}

		$form   = new Form( $form_id );
		$fields = $form->get_fields();

		if ( ! $fields ) {
			return new \WP_Error( 'invalid', __( 'No fields were found for this form.', 'obsidian-forms' ) );
		}

		$values = [];
		$errors = [];

		foreach ( $fields as $field ) {
			$name = sanitize_key( $field->get_attribute( 'fieldName', '' ) );

			if ( ! $name || array_key_exists( $name, $values ) ) {
				continue;
			}

			$value = $field->sanitize( $data[ $name ] ?? '' );

			if ( ! $field->validate( $value ) || ! $this->has_allowed_choice_values( $field, $value ) ) {
				$errors[ $name ] = $field->get_errors();

				if ( ! $errors[ $name ] ) {
					$errors[ $name ] = [ __( 'The selected value is invalid.', 'obsidian-forms' ) ];
				}
				continue;
			}

			$values[ $name ] = $value;
		}

		$errors = apply_filters( 'obsidian_forms_validation_errors', $errors, $form_id, $values );

		if ( $errors ) {
			return new \WP_Error( 'validation_failed', __( 'Validation failed.', 'obsidian-forms' ), $errors );
		}

		do_action( 'obsidian_forms_after_validation', $form_id, $values );
		do_action( 'obsidian_forms_valid_submission', $form_id, $values );

		$field_attributes = array_map(
			static function ( $field ): array {
				return $field->get_attributes();
			},
			$fields
		);
		$entry_id         = Entry::store( $form_id, $field_attributes, $values );

		if ( is_wp_error( $entry_id ) ) {
			return new \WP_Error( 'storage', __( 'The submission could not be stored.', 'obsidian-forms' ) );
		}

		do_action( 'obsidian_forms_after_entry_save', $entry_id, $form_id, $values );

		$email_status = $this->send_notification( $entry_id, $form, $values );
		update_post_meta( $entry_id, '_obsidian_email_status', $email_status );

		do_action( 'obsidian_forms_entry_stored', $entry_id, $form_id, $values );
		do_action( 'obsidian_forms_after_submission', $entry_id, $form_id, $values );

		return $entry_id;
	}

	/**
	 * Attempts an optional plain-text email notification.
	 *
	 * @param int   $entry_id Entry ID.
	 * @param Form  $form     Form model.
	 * @param array $values   Sanitized values.
	 * @return string sent, failed, or disabled.
	 */
	private function send_notification( int $entry_id, Form $form, array $values ): string {
		$settings = $form->get_settings();

		if ( isset( $settings['emailNotificationsEnabled'] ) && ! $settings['emailNotificationsEnabled'] ) {
			return 'disabled';
		}

		$form_id   = $form->get_form_id();
		$recipient = $settings['emailRecipient'] ?? get_option( 'admin_email' );
		$recipient = apply_filters( 'obsidian_forms_notification_recipient', $recipient, $form_id, $values );
		$recipient = apply_filters( 'obsidian_forms_email_to', $recipient, $form_id, $entry_id );

		if ( ! is_email( $recipient ) ) {
			return 'disabled';
		}

		$subject = $settings['emailSubject'] ?? __( 'New form submission: {form_title}', 'obsidian-forms' );
		$subject = str_replace( '{form_title}', $form->get_form_title(), $subject );
		$subject = apply_filters( 'obsidian_forms_email_subject', $subject, $form_id, $entry_id );
		$lines   = [];

		foreach ( $form->get_fields() as $field ) {
			$name = sanitize_key( $field->get_attribute( 'fieldName', '' ) );

			if ( ! array_key_exists( $name, $values ) ) {
				continue;
			}

			$value   = is_array( $values[ $name ] ) ? implode( ', ', $values[ $name ] ) : $values[ $name ];
			$lines[] = sprintf( '%s: %s', $field->get_attribute( 'fieldLabel', $name ), $value );
		}

		$message = implode( "\n", $lines );
		$message = apply_filters( 'obsidian_forms_email_message', $message, $form_id, $entry_id );
		$sent    = wp_mail( $recipient, $subject, $message );

		do_action( 'obsidian_forms_after_email_sent', $sent, $recipient, $subject, $message, $form_id, $entry_id );

		return $sent ? 'sent' : 'failed';
	}

	/**
	 * Prevents submitted choice values that are absent from the saved schema.
	 *
	 * @param object       $field Field model.
	 * @param string|array $value Sanitized value.
	 * @return bool
	 */
	private function has_allowed_choice_values( $field, $value ): bool {
		$type = sanitize_key( $field->get_attribute( 'fieldType', '' ) );

		if ( ! in_array( $type, [ 'select', 'radio', 'checkbox' ], true ) ) {
			return true;
		}

		if ( '' === $value || [] === $value ) {
			return true;
		}

		$allowed = array_map(
			static function ( array $option ): string {
				return sanitize_text_field( ! empty( $option['value'] ) ? $option['value'] : sanitize_title( $option['label'] ?? '' ) );
			},
			$field->get_attribute( 'fieldOptions', [] )
		);
		$values  = is_array( $value ) ? $value : [ $value ];

		return ! array_diff( $values, $allowed );
	}

	/**
	 * Applies a small per-form, per-address submission limit.
	 *
	 * @param int $form_id Source form ID.
	 * @return bool
	 */
	private function check_rate_limit( int $form_id ): bool {
		$address       = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? 'unknown' ) );
		$transient_key = 'obsidian_form_rate_' . md5( $form_id . '|' . $address );
		$attempts      = absint( get_transient( $transient_key ) );
		$max_attempts  = max( 1, absint( apply_filters( 'obsidian_forms_rate_limit_max_attempts', 5, $form_id ) ) );
		$time_window   = max( 60, absint( apply_filters( 'obsidian_forms_rate_limit_time_window', 300, $form_id ) ) );

		if ( $attempts >= $max_attempts ) {
			return false;
		}

		set_transient( $transient_key, $attempts + 1, $time_window );

		return true;
	}

	/**
	 * Redirects with a generic result status.
	 *
	 * @param string $url    Target URL.
	 * @param string $status Submission status.
	 * @return never
	 */
	private function redirect( string $url, string $status ) {
		wp_safe_redirect( add_query_arg( 'obsidian_form_status', $status, $url ) );
		exit;
	}
}
