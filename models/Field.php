<?php

namespace Obsidian_Forms\Models;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Field model to represent a form field.
 *
 * @since   0.1.0
 * @version 0.1.0
 */
class Field {
	/**
	 * The field label.
	 *
	 * @var string
	 */
	protected string $label;

	/**
	 * The field type (e.g., text, email, checkbox).
	 *
	 * @var string
	 */
	protected string $type;

	/**
	 * The field name attribute.
	 *
	 * @var string
	 */
	protected string $name;

	/**
	 * The field value.
	 *
	 * @var mixed
	 */
	protected $value;

	/**
	 * Validation rules for the field.
	 *
	 * @var array
	 */
	protected array $validation_rules = [];

	/**
	 * Constructor.
	 *
	 * @param string $label The field label.
	 * @param string $name The field name attribute.
	 * @param string $type The field type (e.g., text, email, checkbox).
	 * @param mixed $value The default value for the field.
	 */
	public function __construct( string $label, string $name, string $type = 'text', $value = '' ) {
		$this->label = $label;
		$this->name  = $name;
		$this->type  = $type;
		$this->value = $value;
	}

	/**
	 * Adds validation rules for the field.
	 *
	 * @param array $rules Validation rules (e.g., required, email).
	 *
	 * @return void
	 */
	public function add_validation_rules( array $rules ): void {
		$this->validation_rules = array_merge( $this->validation_rules, $rules );
	}

	/**
	 * Renders the field HTML. Can be extended by child classes for specific field types.
	 *
	 * @return string The HTML output for the field.
	 */
	public function render(): string {
		$html = '<label for="' . esc_attr( $this->name ) . '">' . esc_html( $this->label ) . '</label>';
		$html .= '<input type="' . esc_attr( $this->get_input_type() ) . '" name="' . esc_attr( $this->name ) . '" value="' . esc_attr( $this->value ) . '" />';

		return $html;
	}

	/**
	 * Gets the input type for the field. Can be overridden by child classes.
	 *
	 * @return string The field input type.
	 */
	protected function get_input_type(): string {
		return $this->type;
	}

	/**
	 * Validates the field value based on the set rules.
	 * Child classes can extend this method to add custom validation.
	 *
	 * @param mixed $value The value to validate.
	 *
	 * @return bool True if valid, false otherwise.
	 */
	public function validate( $value ): bool {
		foreach ( $this->validation_rules as $rule ) {
			if ( 'required' === $rule && empty( $value ) ) {
				return false;
			}

			// TODO: Add more basic validation rules?
		}

		// TODO: Hook for overriding validation
		return true;
	}
}
