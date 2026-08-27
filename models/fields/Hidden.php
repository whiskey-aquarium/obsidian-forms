<?php

namespace Obsidian_Forms\Models\Fields;

use Obsidian_Forms\Models\Field;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hidden field model that extends the base Field model.
 *
 * @since   0.1.0
 * @version 0.1.0
 */
class Hidden extends Field {

	/**
	 * Constructor for the Hidden field.
	 *
	 * @param string $label The field label.
	 * @param string $name The field name attribute.
	 * @param mixed  $value The default value for the hidden field.
	 */
	public function __construct( string $label, string $name, $value = '' ) {
		parent::__construct( $label, $name, 'hidden', $value );
	}

	/**
	 * Render the hidden field (no label or description).
	 *
	 * @param array $form_settings Form settings from context.
	 *
	 * @return string The HTML output for the field.
	 */
	public function render( array $form_settings = [] ): string {
		return '<input type="hidden" id="' . esc_attr( $this->name ) . '" name="' . esc_attr( $this->name ) . '" value="' . esc_attr( $this->value ) . '" />';
	}

	/**
	 * Sanitize hidden field value.
	 *
	 * @param mixed $value The value to sanitize.
	 *
	 * @return string The sanitized value.
	 */
	public function sanitize( $value ): string {
		return sanitize_text_field( $value );
	}
}

