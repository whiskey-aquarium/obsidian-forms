<?php

namespace Obsidian_Forms\Models\Fields;

use Obsidian_Forms\Models\Field;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Textarea field model that extends the base Field model.
 *
 * @since   0.1.0
 * @version 0.1.0
 */
class Textarea extends Field {

	/**
	 * Constructor for the Textarea field.
	 *
	 * @param string $label The field label.
	 * @param string $name The field name attribute.
	 * @param mixed  $value The default value for the textarea field.
	 */
	public function __construct( string $label, string $name, $value = '' ) {
		parent::__construct( $label, $name, 'textarea', $value );
	}

	/**
	 * Render the textarea input element.
	 *
	 * @param string $placeholder Field placeholder.
	 *
	 * @return string The HTML for the textarea element.
	 */
	protected function render_input( string $placeholder = '' ): string {
		$rows = $this->get_attribute( 'rows', 5 );

		return '<textarea id="' . esc_attr( $this->name ) . '" name="' . esc_attr( $this->name ) . '" placeholder="' . esc_attr( $placeholder ) . '" rows="' . esc_attr( $rows ) . '" class="wp-block-obsidian-form-field__textarea">' . esc_textarea( $this->value ) . '</textarea>';
	}

	/**
	 * Sanitize textarea field value.
	 *
	 * @param mixed $value The value to sanitize.
	 *
	 * @return string The sanitized value.
	 */
	public function sanitize( $value ): string {
		return sanitize_textarea_field( $value );
	}
}

