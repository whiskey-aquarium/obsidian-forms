<?php

namespace Obsidian_Forms\Models;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Email field model that extends the base Field model.
 *
 * @since   0.1.0
 * @version 0.1.0
 */
class Email extends Field {

	/**
	 * Constructor for the Email field.
	 *
	 * @param string $label The field label.
	 * @param string $name The field name attribute.
	 * @param mixed $value The default value for the email field.
	 */
	public function __construct( string $label, string $name, $value = '' ) {
		parent::__construct( $label, $name, 'email', $value );

		$this->add_validation_rules( [ 'email' ] );
	}

	/**
	 * Override the validate method for email-specific validation.
	 *
	 * @param mixed $value The value to validate.
	 *
	 * @return bool True if valid, false otherwise.
	 */
	public function validate( $value ): bool {
		if ( ! parent::validate( $value ) ) {
			return false;
		}

		// Ensure value is a valid email.
		if ( ! filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
			return false;
		}

		return true;
	}
}
