<?php

namespace Obsidian_Forms\Models\Fields;

use Obsidian_Forms\Models\Field;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Text field model that extends the base Field model.
 *
 * @since   0.1.0
 * @version 0.1.0
 */
class Text extends Field {

	/**
	 * Constructor for the Text field.
	 *
	 * @param string $label The field label.
	 * @param string $name The field name attribute.
	 * @param mixed  $value The default value for the text field.
	 */
	public function __construct( string $label, string $name, $value = '' ) {
		parent::__construct( $label, $name, 'text', $value );
	}

	/**
	 * Sanitize text field value.
	 *
	 * @param mixed $value The value to sanitize.
	 *
	 * @return string The sanitized value.
	 */
	public function sanitize( $value ): string {
		return sanitize_text_field( $value );
	}
}
