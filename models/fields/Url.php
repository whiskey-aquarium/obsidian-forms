<?php

namespace Obsidian_Forms\Models\Fields;

use Obsidian_Forms\Models\Field;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * URL field model that extends the base Field model.
 *
 * @since   0.1.0
 * @version 0.1.0
 */
class Url extends Field {

	/**
	 * Constructor for the URL field.
	 *
	 * @param string $label The field label.
	 * @param string $name The field name attribute.
	 * @param mixed  $value The default value for the URL field.
	 */
	public function __construct( string $label, string $name, $value = '' ) {
		parent::__construct( $label, $name, 'url', $value );
	}

	/**
	 * Sanitize URL field value.
	 *
	 * @param mixed $value The value to sanitize.
	 *
	 * @return string The sanitized value.
	 */
	public function sanitize( $value ): string {
		return esc_url_raw( $value );
	}

	/**
	 * Validate URL field value.
	 *
	 * @param mixed $value The value to validate.
	 *
	 * @return bool True if valid, false otherwise.
	 */
	public function validate( $value ): bool {
		if ( ! parent::validate( $value ) ) {
			return false;
		}

		// Validate URL format.
		if ( ! empty( $value ) && ! filter_var( $value, FILTER_VALIDATE_URL ) ) {
			$this->add_error( __( 'Please enter a valid URL.', 'obsidian-forms' ) );

			return false;
		}

		return true;
	}
}

