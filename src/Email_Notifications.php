<?php

namespace Obsidian_Forms;

use Obsidian_Forms\Models\Form;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles sending email notifications when forms are submitted.
 *
 * @since   0.1.0
 * @version 0.1.0
 */
class Email_Notifications {

	/**
	 * Entry ID.
	 *
	 * @var int
	 */
	protected int $entry_id = 0;

	/**
	 * Entry data.
	 *
	 * @var array
	 */
	protected array $entry_data = [];

	/**
	 * Entry meta.
	 *
	 * @var array
	 */
	protected array $entry_meta = [];

	/**
	 * Form ID.
	 *
	 * @var int
	 */
	protected int $form_id = 0;

	/**
	 * Form model.
	 *
	 * @var Form|null
	 */
	protected ?Form $form = null;

	/**
	 * Send email notification for an entry.
	 *
	 * @param int $entry_id The entry ID.
	 *
	 * @return bool True if email sent, false otherwise.
	 */
	public function send( int $entry_id ): bool {
		$this->entry_id = $entry_id;

		// Load entry data.
		$entry_model = new Entry( $entry_id );
		$entry_full = $entry_model->get_entry( $entry_id );

		if ( ! $entry_full ) {
			return false;
		}

		$this->entry_data = $entry_full['entry'];
		$this->entry_meta = $entry_full['meta'];
		$this->form_id = absint( $this->entry_data['form_id'] ?? 0 );

		// Load form.
		$this->form = new Form( $this->form_id );

		// Get notification settings.
		$settings = $this->get_notification_settings( $this->form_id );

		// Check if notifications are enabled.
		if ( empty( $settings['emailNotificationsEnabled'] ) ) {
			return false;
		}

		// Get recipient email.
		$to = $this->get_recipient_email( $this->form_id );

		if ( empty( $to ) || ! is_email( $to ) ) {
			return false;
		}

		// Get subject.
		$subject = $settings['emailSubject'] ?? sprintf( __( 'New form submission: %s', 'obsidian-forms' ), $this->form->get_form_title() );
		$subject = $this->parse_email_template( $subject );

		// Get message.
		$message = $settings['emailMessage'] ?? $this->get_default_template();
		$message = $this->parse_email_template( $message );

		// Get headers.
		$headers = [ 'Content-Type: text/html; charset=UTF-8' ];

		// Allow filtering of email parameters.
		$to = apply_filters( 'obsidian_forms_email_to', $to, $this->form_id, $entry_id );
		$subject = apply_filters( 'obsidian_forms_email_subject', $subject, $this->form_id, $entry_id );
		$message = apply_filters( 'obsidian_forms_email_message', $message, $this->form_id, $entry_id );
		$headers = apply_filters( 'obsidian_forms_email_headers', $headers, $this->form_id, $entry_id );

		// Send email.
		$sent = wp_mail( $to, $subject, $message, $headers );

		// Fire action after email sent (or attempted).
		do_action( 'obsidian_forms_after_email_sent', $sent, $to, $subject, $message, $this->form_id, $entry_id );

		return $sent;
	}

	/**
	 * Get notification settings for a form.
	 *
	 * @param int $form_id The form ID.
	 *
	 * @return array
	 */
	protected function get_notification_settings( int $form_id ): array {
		$form = new Form( $form_id );
		$settings = $form->get_settings();

		return $settings;
	}

	/**
	 * Parse email template and replace merge tags.
	 *
	 * @param string $template The template string.
	 *
	 * @return string The parsed template.
	 */
	protected function parse_email_template( string $template ): string {
		// Replace {field:field_name} with field values.
		$template = preg_replace_callback(
			'/\{field:([^\}]+)\}/',
			function ( $matches ) {
				$field_name = $matches[1];

				if ( isset( $this->entry_meta[ $field_name ] ) ) {
					$value = $this->entry_meta[ $field_name ]['value'];

					if ( is_array( $value ) ) {
						return implode( ', ', $value );
					}

					return $value;
				}

				return '';
			},
			$template
		);

		// Replace {form_title}.
		$template = str_replace( '{form_title}', $this->form->get_form_title(), $template );

		// Replace {entry_id}.
		$template = str_replace( '{entry_id}', $this->entry_id, $template );

		// Replace {submission_date}.
		$date_format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
		$submission_date = isset( $this->entry_data['created_at'] ) ? mysql2date( $date_format, $this->entry_data['created_at'] ) : '';
		$template = str_replace( '{submission_date}', $submission_date, $template );

		// Replace {all_fields}.
		$template = str_replace( '{all_fields}', $this->get_all_fields_html(), $template );

		// Allow custom merge tags via filter.
		$template = apply_filters( 'obsidian_forms_email_template', $template, $this->entry_id, $this->form_id );

		return $template;
	}

	/**
	 * Get default email template.
	 *
	 * @return string
	 */
	protected function get_default_template(): string {
		$template = '<h2>' . __( 'New Form Submission', 'obsidian-forms' ) . '</h2>';
		$template .= '<p><strong>' . __( 'Form:', 'obsidian-forms' ) . '</strong> {form_title}</p>';
		$template .= '<p><strong>' . __( 'Submitted:', 'obsidian-forms' ) . '</strong> {submission_date}</p>';
		$template .= '<hr>';
		$template .= '{all_fields}';

		return $template;
	}

	/**
	 * Get all fields formatted as HTML table.
	 *
	 * @return string
	 */
	protected function get_all_fields_html(): string {
		if ( empty( $this->entry_meta ) ) {
			return '';
		}

		$html = '<table style="width: 100%; border-collapse: collapse;">';
		$html .= '<thead>';
		$html .= '<tr>';
		$html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #f2f2f2;">' . __( 'Field', 'obsidian-forms' ) . '</th>';
		$html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #f2f2f2;">' . __( 'Value', 'obsidian-forms' ) . '</th>';
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody>';

		foreach ( $this->entry_meta as $field_name => $field_data ) {
			$label = $field_data['label'] ?? $field_name;
			$value = $field_data['value'] ?? '';

			// Handle arrays.
			if ( is_array( $value ) ) {
				$value = implode( ', ', $value );
			}

			$html .= '<tr>';
			$html .= '<td style="border: 1px solid #ddd; padding: 8px;"><strong>' . esc_html( $label ) . '</strong></td>';
			$html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . esc_html( $value ) . '</td>';
			$html .= '</tr>';
		}

		$html .= '</tbody>';
		$html .= '</table>';

		return $html;
	}

	/**
	 * Get recipient email address.
	 *
	 * @param int $form_id The form ID.
	 *
	 * @return string
	 */
	protected function get_recipient_email( int $form_id ): string {
		$settings = $this->get_notification_settings( $form_id );

		// Check for custom recipient.
		if ( ! empty( $settings['emailRecipient'] ) ) {
			return $settings['emailRecipient'];
		}

		// Default to admin email.
		return get_option( 'admin_email' );
	}
}

