<?php

namespace Obsidian_Forms\Models\Fields;

use Obsidian_Forms\Models\Field;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Number field model that extends the base Field model.
 *
 * @since   0.1.0
 * @version 0.1.0
 */
class Number extends Field {

	/**
	 * Constructor for the Number field.
	 *
	 * @param string $label The field label.
	 * @param string $name The field name attribute.
	 * @param mixed  $value The default value for the number field.
	 */
	public function __construct( string $label, string $name, $value = '' ) {
		parent::__construct( $label, $name, 'number', $value );
	}

	/**
	 * Render the number input element.
	 *
	 * @param string $placeholder Field placeholder.
	 *
	 * @return string The HTML for the number input element.
	 */
	protected function render_input( string $placeholder = '' ): string {
		$extra_props = $this->get_attribute( 'extraProps', [] );
		$min = isset( $extra_props['min'] ) ? ' min="' . esc_attr( $extra_props['min'] ) . '"' : '';
		$max = isset( $extra_props['max'] ) ? ' max="' . esc_attr( $extra_props['max'] ) . '"' : '';
		$step = isset( $extra_props['step'] ) ? ' step="' . esc_attr( $extra_props['step'] ) . '"' : '';

		return '<input type="number" id="' . esc_attr( $this->name ) . '" name="' . esc_attr( $this->name ) . '" value="' . esc_attr( $this->value ) . '" placeholder="' . esc_attr( $placeholder ) . '"' . $min . $max . $step . ' class="wp-block-obsidian-form-field__input" />';
	}

	/**
	 * Sanitize number field value.
	 *
	 * @param mixed $value The value to sanitize.
	 *
	 * @return float|int|string The sanitized value.
	 */
	public function sanitize( $value ) {
		if ( '' === $value ) {
			return '';
		}

		return is_numeric( $value ) ? $value : 0;
	}

	/**
	 * Validate number field value.
	 *
	 * @param mixed $value The value to validate.
	 *
	 * @return bool True if valid, false otherwise.
	 */
	public function validate( $value ): bool {
		if ( ! parent::validate( $value ) ) {
			return false;
		}

		// Validate number format.
		if ( ! empty( $value ) && ! is_numeric( $value ) ) {
			$this->add_error( __( 'Please enter a valid number.', 'obsidian-forms' ) );

			return false;
		}

		// Validate min/max if set.
		$extra_props = $this->get_attribute( 'extraProps', [] );

		if ( isset( $extra_props['min'] ) && $value < $extra_props['min'] ) {
			$this->add_error( sprintf( __( 'Please enter a number greater than or equal to %s.', 'obsidian-forms' ), $extra_props['min'] ) );

			return false;
		}

		if ( isset( $extra_props['max'] ) && $value > $extra_props['max'] ) {
			$this->add_error( sprintf( __( 'Please enter a number less than or equal to %s.', 'obsidian-forms' ), $extra_props['max'] ) );

			return false;
		}

		return true;
	}
}

