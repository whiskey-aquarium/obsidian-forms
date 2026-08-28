<?php

namespace Obsidian_Forms\Models\Fields;

use Obsidian_Forms\Models\Field;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Select field model that extends the base Field model.
 *
 * @since   0.1.0
 * @version 0.1.0
 */
class Select extends Field {

	/**
	 * Constructor for the Select field.
	 *
	 * @param string $label The field label.
	 * @param string $name The field name attribute.
	 * @param mixed  $value The default value for the select field.
	 */
	public function __construct( string $label, string $name, $value = '' ) {
		parent::__construct( $label, $name, 'select', $value );
	}

	/**
	 * Render the select input element.
	 *
	 * @param string $placeholder Field placeholder.
	 *
	 * @return string The HTML for the select element.
	 */
	protected function render_input( string $placeholder = '' ): string {
		$options = $this->get_attribute( 'fieldOptions', [] );

		$html = '<select id="' . esc_attr( $this->name ) . '" name="' . esc_attr( $this->name ) . '" class="wp-block-obsidian-form-field__select">';

		if ( ! empty( $placeholder ) ) {
			$html .= '<option value="">' . esc_html( $placeholder ) . '</option>';
		}

		foreach ( $options as $option ) {
			$selected = ( isset( $option['value'] ) && $option['value'] === $this->value ) ? ' selected' : '';
			$html    .= '<option value="' . esc_attr( $option['value'] ?? '' ) . '"' . $selected . '>' . esc_html( $option['label'] ?? '' ) . '</option>';
		}

		$html .= '</select>';

		return $html;
	}

	/**
	 * Sanitize select field value.
	 *
	 * @param mixed $value The value to sanitize.
	 *
	 * @return string The sanitized value.
	 */
	public function sanitize( $value ): string {
		return sanitize_text_field( $value );
	}

	/**
	 * Validate select field value.
	 *
	 * @param mixed $value The value to validate.
	 *
	 * @return bool True if valid, false otherwise.
	 */
	public function validate( $value ): bool {
		if ( ! parent::validate( $value ) ) {
			return false;
		}

		// Validate that the value is in the options list.
		$options = $this->get_attribute( 'fieldOptions', [] );

		if ( '' === $value || empty( $options ) ) {
			return true;
		}

		$valid_values = array_column( $options, 'value' );

		if ( ! in_array( $value, $valid_values, true ) ) {
			$this->add_error( __( 'Please select a valid option.', 'obsidian-forms' ) );

			return false;
		}

		return true;
	}
}
