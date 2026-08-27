<?php

namespace Obsidian_Forms;

use Obsidian_Forms\Models\Form;
use WP_Error;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles form submissions via both POST and AJAX.
 *
 * @since   0.1.0
 * @version 0.1.0
 */
class Form_Submission {

	/**
	 * Validation errors.
	 *
	 * @var array
	 */
	protected array $validation_errors = [];

	/**
	 * The submitted form data.
	 *
	 * @var array
	 */
	protected array $form_data = [];

	/**
	 * The form ID being submitted.
	 *
	 * @var int
	 */
	protected int $form_id = 0;

	/**
	 * Initialize the submission handler.
	 *
	 * @return void
	 */
	public function initialize(): void {
		// Hook into template_redirect for standard POST handling.
		add_action( 'template_redirect', [ $this, 'handle_post_submission' ] );
	}

	/**
	 * Handle standard POST form submissions.
	 *
	 * @return void
	 */
	public function handle_post_submission(): void {
		// Skip if this is a REST API request (AJAX submissions are handled by REST endpoint).
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return;
		}

		// Check if this is a form submission.
		if ( 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
			return;
		}

		if ( ! isset( $_POST['obsidian_form_id'] ) || ! isset( $_POST['obsidian_form_nonce'] ) ) {
			return;
		}

		$form_id = absint( $_POST['obsidian_form_id'] );

		// Verify nonce.
		if ( ! wp_verify_nonce( $_POST['obsidian_form_nonce'], 'obsidian_form_submit_' . $form_id ) ) {
			return;
		}

		// Process the submission.
		$result = $this->process_submission( $form_id, $_POST );

		// Handle redirect based on result.
		if ( is_wp_error( $result ) ) {
			// Redirect with error.
			$redirect_url = add_query_arg( 'obsidian_form_error', $form_id, wp_get_referer() );
			wp_safe_redirect( $redirect_url );
			exit;
		}

		// Check if there's a custom redirect URL.
		$form = new Form( $form_id );
		$settings = $form->get_settings();
		$redirect_url = $settings['redirectUrl'] ?? '';

		if ( ! empty( $redirect_url ) ) {
			wp_safe_redirect( esc_url_raw( $redirect_url ) );
			exit;
		}

		// Default: redirect back with success message.
		$redirect_url = add_query_arg( 'obsidian_form_success', $form_id, wp_get_referer() );
		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Process form submission (used by both POST and AJAX).
	 *
	 * @param int   $form_id The form ID.
	 * @param array $data    The submitted data.
	 *
	 * @return int|WP_Error Entry ID on success, WP_Error on failure.
	 */
	public function process_submission( int $form_id, array $data ) {
		$this->form_id = $form_id;
		$this->form_data = $data;
		$this->validation_errors = [];

		// Hook before submission.
		do_action( 'obsidian_forms_before_submission', $form_id, $data );

		// Rate limiting check.
		if ( ! $this->check_rate_limit() ) {
			return new WP_Error( 'rate_limit', __( 'Too many submissions. Please try again later.', 'obsidian-forms' ) );
		}

		// Validate submission.
		$validation_result = $this->validate_submission( $form_id, $data );

		if ( is_wp_error( $validation_result ) ) {
			return $validation_result;
		}

		// Hook after validation.
		do_action( 'obsidian_forms_after_validation', $form_id, $data );

		// Save entry to database.
		$entry_id = $this->save_entry( $form_id, $data );

		if ( is_wp_error( $entry_id ) ) {
			return $entry_id;
		}

		// Send email notifications.
		$this->send_notifications( $entry_id );

		// Hook after submission.
		do_action( 'obsidian_forms_after_submission', $entry_id, $form_id, $data );

		return $entry_id;
	}

	/**
	 * Validate form submission.
	 *
	 * @param int   $form_id The form ID.
	 * @param array $data    The submitted data.
	 *
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	protected function validate_submission( int $form_id, array $data ) {
		// Create form model.
		$form = new Form( $form_id );

		if ( empty( $form->get_form_id() ) ) {
			return new WP_Error( 'invalid_form', __( 'Invalid form.', 'obsidian-forms' ) );
		}

		// Get form fields.
		$fields = $form->get_fields();

		if ( empty( $fields ) ) {
			return new WP_Error( 'no_fields', __( 'No fields found for this form.', 'obsidian-forms' ) );
		}

		// Validate each field.
		foreach ( $fields as $field ) {
			$field_name = $field->get_attribute( 'fieldName', '' );

			if ( empty( $field_name ) ) {
				continue;
			}

			// Get submitted value.
			$value = $data[ $field_name ] ?? '';

			// Sanitize value.
			$value = $field->sanitize( $value );

			// Validate value.
			$is_valid = $field->validate( $value );

			if ( ! $is_valid ) {
				$errors = $field->get_errors();

				foreach ( $errors as $error ) {
					$this->validation_errors[ $field_name ] = $error;
				}
			}
		}

		// Allow custom validation via filter.
		$this->validation_errors = apply_filters( 'obsidian_forms_validation_errors', $this->validation_errors, $form_id, $data );

		if ( ! empty( $this->validation_errors ) ) {
			return new WP_Error( 'validation_failed', __( 'Validation failed.', 'obsidian-forms' ), $this->validation_errors );
		}

		return true;
	}

	/**
	 * Save entry to database.
	 *
	 * @param int   $form_id The form ID.
	 * @param array $data    The submitted data.
	 *
	 * @return int|WP_Error Entry ID on success, WP_Error on failure.
	 */
	protected function save_entry( int $form_id, array $data ) {
		// Hook before entry save.
		do_action( 'obsidian_forms_before_entry_save', $form_id, $data );

		// Create entry using Entry model.
		$entry = new Entry();
		$entry_id = $entry->create( $form_id, $data );

		if ( is_wp_error( $entry_id ) ) {
			return $entry_id;
		}

		// Hook after entry save.
		do_action( 'obsidian_forms_after_entry_save', $entry_id, $form_id, $data );

		return $entry_id;
	}

	/**
	 * Send email notifications.
	 *
	 * @param int $entry_id The entry ID.
	 *
	 * @return void
	 */
	protected function send_notifications( int $entry_id ): void {
		$notifications = new Email_Notifications();
		$notifications->send( $entry_id );
	}

	/**
	 * Get validation errors.
	 *
	 * @return array
	 */
	public function get_validation_errors(): array {
		return $this->validation_errors;
	}

	/**
	 * Check rate limit for submissions.
	 *
	 * @return bool True if under limit, false if over limit.
	 */
	protected function check_rate_limit(): bool {
		// Get IP address.
		$ip_address = $this->get_ip_address();

		// Get transient key.
		$transient_key = 'obsidian_form_rate_limit_' . md5( $ip_address );

		// Get current attempt count.
		$attempts = get_transient( $transient_key );

		if ( false === $attempts ) {
			$attempts = 0;
		}

		// Allow filtering of rate limit.
		$max_attempts = apply_filters( 'obsidian_forms_rate_limit_max_attempts', 5 );
		$time_window = apply_filters( 'obsidian_forms_rate_limit_time_window', 300 ); // 5 minutes.

		// Check if over limit.
		if ( $attempts >= $max_attempts ) {
			return false;
		}

		// Increment attempts.
		++$attempts;
		set_transient( $transient_key, $attempts, $time_window );

		return true;
	}

	/**
	 * Get the user's IP address.
	 *
	 * @return string
	 */
	protected function get_ip_address(): string {
		$ip = '';

		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return sanitize_text_field( $ip );
	}
}

