<?php

namespace Obsidian_Forms\Models\Fields;

use Obsidian_Forms\Models\Field;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Checkbox field model that extends the base Field model.
 *
 * @since   0.1.0
 * @version 0.1.0
 */
class Checkbox extends Field {

	/**
	 * Constructor for the Checkbox field.
	 *
	 * @param string $label The field label.
	 * @param string $name The field name attribute.
	 * @param mixed  $value The default value for the checkbox field.
	 */
	public function __construct( string $label, string $name, $value = '' ) {
		parent::__construct( $label, $name, 'checkbox', $value );
	}

	/**
	 * Render the checkbox input element.
	 *
	 * @param string $placeholder Field placeholder.
	 *
	 * @return string The HTML for the checkbox element.
	 */
	protected function render_input( string $placeholder = '' ): string {
		$options = $this->get_attribute( 'fieldOptions', [] );
		$extra_props = $this->get_attribute( 'extraProps', [] );
		$layout = $extra_props['checkboxesLayout'] ?? 'stacked';

		if ( empty( $options ) ) {
			return '';
		}

		$html = '<div class="wp-block-obsidian-form-field__checkboxes wp-block-obsidian-form-field__checkboxes--' . esc_attr( $layout ) . '">';

		foreach ( $options as $option ) {
			$value = $option['value'] ?? '';
			$label = $option['label'] ?? '';
			$checked = is_array( $this->value ) && in_array( $value, $this->value, true ) ? ' checked' : '';

			$html .= '<div class="wp-block-obsidian-form-field__checkbox">';
			$html .= '<input type="checkbox" id="' . esc_attr( $this->name . '-' . $value ) . '" name="' . esc_attr( $this->name ) . '[]" value="' . esc_attr( $value ) . '"' . $checked . ' />';
			$html .= '<label for="' . esc_attr( $this->name . '-' . $value ) . '">' . esc_html( $label ) . '</label>';
			$html .= '</div>';
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Sanitize checkbox field value.
	 *
	 * @param mixed $value The value to sanitize.
	 *
	 * @return array The sanitized value.
	 */
	public function sanitize( $value ): array {
		if ( ! is_array( $value ) ) {
			return [];
		}

		return array_map( 'sanitize_text_field', $value );
	}

	/**
	 * Validate checkbox field value.
	 *
	 * @param mixed $value The value to validate.
	 *
	 * @return bool True if valid, false otherwise.
	 */
	public function validate( $value ): bool {
		// For required checkboxes, ensure at least one is selected.
		if ( in_array( 'required', $this->validation_rules, true ) ) {
			if ( empty( $value ) || ! is_array( $value ) ) {
				$this->add_error( __( 'Please select at least one option.', 'obsidian-forms' ) );

				return false;
			}
		}

		return true;
	}
}

