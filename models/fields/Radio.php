<?php

namespace Obsidian_Forms\Models\Fields;

use Obsidian_Forms\Models\Field;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Radio field model that extends the base Field model.
 *
 * @since   0.1.0
 * @version 0.1.0
 */
class Radio extends Field {

	/**
	 * Constructor for the Radio field.
	 *
	 * @param string $label The field label.
	 * @param string $name The field name attribute.
	 * @param mixed  $value The default value for the radio field.
	 */
	public function __construct( string $label, string $name, $value = '' ) {
		parent::__construct( $label, $name, 'radio', $value );
	}

	/**
	 * Render the radio input element.
	 *
	 * @param string $placeholder Field placeholder.
	 *
	 * @return string The HTML for the radio element.
	 */
	protected function render_input( string $placeholder = '' ): string {
		$options     = $this->get_attribute( 'fieldOptions', [] );
		$extra_props = $this->get_attribute( 'extraProps', [] );
		$layout      = $extra_props['radioLayout'] ?? 'stacked';

		if ( empty( $options ) ) {
			return '';
		}

		$html = '<div class="wp-block-obsidian-form-field__radios wp-block-obsidian-form-field__radios--' . esc_attr( $layout ) . '">';

		foreach ( $options as $option ) {
			$value   = $option['value'] ?? '';
			$label   = $option['label'] ?? '';
			$checked = ( $value === $this->value ) ? ' checked' : '';

			$html .= '<div class="wp-block-obsidian-form-field__radio">';
			$html .= '<input type="radio" id="' . esc_attr( $this->name . '-' . $value ) . '" name="' . esc_attr( $this->name ) . '" value="' . esc_attr( $value ) . '"' . $checked . ' />';
			$html .= '<label for="' . esc_attr( $this->name . '-' . $value ) . '">' . esc_html( $label ) . '</label>';
			$html .= '</div>';
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Sanitize radio field value.
	 *
	 * @param mixed $value The value to sanitize.
	 *
	 * @return string The sanitized value.
	 */
	public function sanitize( $value ): string {
		return sanitize_text_field( $value );
	}

	/**
	 * Validate radio field value.
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
