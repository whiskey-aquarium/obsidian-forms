<?php

namespace Obsidian_Forms;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Validates and delivers front-end form submissions.
 */
final class Submission {
	/**
	 * Registers submission handlers.
	 *
	 * @return void
	 */
	public function initialize(): void {
		add_action( 'admin_post_obsidian_forms_submit', [ $this, 'handle' ] );
		add_action( 'admin_post_nopriv_obsidian_forms_submit', [ $this, 'handle' ] );
	}

	/**
	 * Handles a form submission and redirects back to the originating page.
	 *
	 * @return void
	 */
	public function handle(): void {
		$form_id  = absint( $_POST['obsidian_form_id'] ?? 0 );
		$redirect = wp_validate_redirect(
			esc_url_raw( wp_unslash( $_POST['obsidian_form_redirect'] ?? '' ) ),
			home_url( '/' )
		);

		if (
			! $form_id ||
			! isset( $_POST['obsidian_form_nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['obsidian_form_nonce'] ) ), 'obsidian_form_submit_' . $form_id )
		) {
			$this->redirect( $redirect, 'invalid' );
		}

		$form = get_post( $form_id );

		if ( ! $form instanceof \WP_Post || 'obsidian_form' !== $form->post_type || 'publish' !== $form->post_status ) {
			$this->redirect( $redirect, 'invalid' );
		}

		// Honeypot field: silently accept bot submissions without sending mail.
		if ( ! empty( $_POST['obsidian_form_website'] ) ) {
			$this->redirect( $redirect, 'success' );
		}

		$fields = $this->get_fields( parse_blocks( $form->post_content ) );
		$values = [];
		$errors = [];

		foreach ( $fields as $field ) {
			$name = sanitize_key( $field['fieldName'] ?? '' );

			if ( ! $name || isset( $values[ $name ] ) ) {
				continue;
			}

			$type  = sanitize_key( $field['fieldType'] ?? 'text' );
			$value = $_POST[ $name ] ?? ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified above.
			if ( is_array( $value ) ) {
				$value = array_map( 'sanitize_text_field', wp_unslash( $value ) );
			} elseif ( 'textarea' === $type ) {
				$value = sanitize_textarea_field( wp_unslash( $value ) );
			} else {
				$value = sanitize_text_field( wp_unslash( $value ) );
			}

			if ( ! empty( $field['isRequired'] ) && ( '' === $value || [] === $value ) ) {
				$errors[] = $name;
				continue;
			}

			if ( '' !== $value && [] !== $value && ! $this->is_valid( $type, $value, $field ) ) {
				$errors[] = $name;
				continue;
			}

			$values[ $name ] = $value;
		}

		if ( $errors ) {
			$this->redirect( $redirect, 'validation' );
		}

		$recipient = apply_filters( 'obsidian_forms_notification_recipient', get_option( 'admin_email' ), $form_id, $values );
		$subject   = sprintf(
			/* translators: %s: form title. */
			__( 'New submission: %s', 'obsidian-forms' ),
			get_the_title( $form )
		);
		$message = '';

		foreach ( $values as $name => $value ) {
			$message .= sprintf(
				"%s: %s\n",
				$name,
				is_array( $value ) ? implode( ', ', $value ) : $value
			);
		}

		/**
		 * Fires after a valid submission and before notification delivery.
		 *
		 * @param int   $form_id Form post ID.
		 * @param array $values  Sanitized field values.
		 */
		do_action( 'obsidian_forms_valid_submission', $form_id, $values );

		$sent = is_email( $recipient ) && wp_mail( $recipient, $subject, $message );
		$this->redirect( $redirect, $sent ? 'success' : 'delivery' );
	}

	/**
	 * Collects field attributes recursively from parsed blocks.
	 *
	 * @param array $blocks Parsed blocks.
	 * @return array
	 */
	private function get_fields( array $blocks ): array {
		$fields = [];

		foreach ( $blocks as $block ) {
			if ( 'obsidian-form/field' === ( $block['blockName'] ?? '' ) ) {
				$fields[] = $block['attrs'] ?? [];
			}

			if ( ! empty( $block['innerBlocks'] ) ) {
				$fields = array_merge( $fields, $this->get_fields( $block['innerBlocks'] ) );
			}
		}

		return $fields;
	}

	/**
	 * Validates one sanitized field value.
	 *
	 * @param string       $type  Field type.
	 * @param string|array $value Sanitized value.
	 * @param array        $field Field attributes.
	 * @return bool
	 */
	private function is_valid( string $type, $value, array $field ): bool {
		if ( 'email' === $type ) {
			return false !== is_email( $value );
		}

		if ( 'url' === $type ) {
			return false !== filter_var( $value, FILTER_VALIDATE_URL );
		}

		if ( in_array( $type, [ 'number', 'range' ], true ) ) {
			return is_numeric( $value );
		}

		if ( in_array( $type, [ 'select', 'radio', 'checkbox' ], true ) ) {
			$allowed = array_map(
				static function ( array $option ): string {
					return sanitize_text_field( ! empty( $option['value'] ) ? $option['value'] : sanitize_title( $option['label'] ?? '' ) );
				},
				$field['fieldOptions'] ?? []
			);
			$values  = is_array( $value ) ? $value : [ $value ];

			return ! array_diff( $values, $allowed );
		}

		return is_string( $value );
	}

	/**
	 * Redirects to the originating page with a generic result code.
	 *
	 * @param string $url    Redirect URL.
	 * @param string $status Submission status.
	 * @return never
	 */
	private function redirect( string $url, string $status ) {
		wp_safe_redirect( add_query_arg( 'obsidian_form_status', $status, $url ) );
		exit;
	}
}
