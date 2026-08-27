<?php

namespace Obsidian_Forms\Models\Fields;

use Obsidian_Forms\Models\Field;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tel (telephone) field model that extends the base Field model.
 *
 * @since   0.1.0
 * @version 0.1.0
 */
class Tel extends Field {

	/**
	 * Constructor for the Tel field.
	 *
	 * @param string $label The field label.
	 * @param string $name The field name attribute.
	 * @param mixed  $value The default value for the tel field.
	 */
	public function __construct( string $label, string $name, $value = '' ) {
		parent::__construct( $label, $name, 'tel', $value );
	}

	/**
	 * Sanitize tel field value.
	 *
	 * @param mixed $value The value to sanitize.
	 *
	 * @return string The sanitized value.
	 */
	public function sanitize( $value ): string {
		return sanitize_text_field( $value );
	}

	/**
	 * Validate tel field value.
	 *
	 * @param mixed $value The value to validate.
	 *
	 * @return bool True if valid, false otherwise.
	 */
	public function validate( $value ): bool {
		if ( ! parent::validate( $value ) ) {
			return false;
		}

		// Basic phone number validation - allow digits, spaces, dashes, parentheses, and plus sign.
		if ( ! empty( $value ) && ! preg_match( '/^[0-9\s\-\(\)\+]+$/', $value ) ) {
			$this->add_error( __( 'Please enter a valid phone number.', 'obsidian-forms' ) );

			return false;
		}

		return true;
	}
}

