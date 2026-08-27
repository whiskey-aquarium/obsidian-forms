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
	 * Block attributes from the field block.
	 *
	 * @var array
	 */
	protected array $attributes = [];

	/**
	 * Validation errors for the field.
	 *
	 * @var array
	 */
	protected array $errors = [];

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
	 * Set block attributes.
	 *
	 * @param array $attributes Block attributes.
	 *
	 * @return void
	 */
	public function set_attributes( array $attributes ): void {
		$this->attributes = $attributes;
	}

	/**
	 * Get block attributes.
	 *
	 * @return array
	 */
	public function get_attributes(): array {
		return $this->attributes;
	}

	/**
	 * Get a specific attribute.
	 *
	 * @param string $key     Attribute key.
	 * @param mixed  $fallback Fallback value if not set.
	 *
	 * @return mixed
	 */
	public function get_attribute( string $key, $fallback = null ) {
		return $this->attributes[ $key ] ?? $fallback;
	}

	/**
	 * Renders the field HTML with form settings support.
	 *
	 * @param array $form_settings Form settings from context.
	 *
	 * @return string The HTML output for the field.
	 */
	public function render( array $form_settings = [] ): string {
		$label_placement       = $form_settings['labelPlacement'] ?? 'top';
		$description_placement = $form_settings['descriptionPlacement'] ?? 'bottom';
		$required_indicator    = $form_settings['requiredIndicator'] ?? '*';
		$is_required           = $this->get_attribute( 'isRequired', false );
		$field_description     = $this->get_attribute( 'fieldDescription', '' );
		$field_placeholder     = $this->get_attribute( 'fieldPlaceholder', '' );

		// Allow filtering of field rendering.
		$custom_render = apply_filters( 'obsidian_forms_field_render', null, $this, $form_settings );

		if ( null !== $custom_render ) {
			return $custom_render;
		}

		$custom_type_render = apply_filters( 'obsidian_forms_field_render_' . $this->type, null, $this, $form_settings );

		if ( null !== $custom_type_render ) {
			return $custom_type_render;
		}

		// Build field classes.
		$field_classes = [
			'wp-block-obsidian-form-field',
			'obsidian-forms-field__' . $this->type,
			'obsidian-forms-field__label-' . $label_placement,
		];

		if ( $is_required ) {
			$field_classes[] = 'obsidian-forms-field__required';
		}

		$field_classes = apply_filters( 'obsidian_forms_field_classes', $field_classes, $this );

		// Build field styles - use custom property for width if field has explicit width.
		$field_width = $this->get_attribute( 'fieldWidth', '' );
		$field_style = '';

		if ( ! empty( $field_width ) ) {
			$field_style = ' style="flex: ' . esc_attr( $field_width ) . ';"';
		} else {
			// Use the custom property from parent field group if no explicit width.
			$field_style = ' style="flex: var(--of-field-flex, 100%);"';
		}

		// Hook before field.
		do_action( 'obsidian_forms_before_field', $this, $form_settings );
		do_action( 'obsidian_forms_before_field_' . $this->type, $this, $form_settings );

		ob_start();
		?>
		<div class="<?php echo esc_attr( implode( ' ', $field_classes ) ); ?>"<?php echo $field_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built exclusively from an escaped numeric/style attribute above. ?>>
			<label for="<?php echo esc_attr( $this->name ); ?>" class="wp-block-obsidian-form-field__label">
				<?php echo esc_html( $this->label ); ?>
				<?php if ( $is_required ) : ?>
					<span class="required-indicator"><?php echo esc_html( $required_indicator ); ?></span>
				<?php endif; ?>
			</label>

			<?php if ( 'top' === $description_placement && ! empty( $field_description ) ) : ?>
				<div class="wp-block-obsidian-form-field__description">
					<small><?php echo esc_html( $field_description ); ?></small>
				</div>
			<?php endif; ?>

			<?php echo $this->render_input( $field_placeholder ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Field model renderers escape every dynamic attribute and value. ?>

			<?php if ( 'bottom' === $description_placement && ! empty( $field_description ) ) : ?>
				<div class="wp-block-obsidian-form-field__description">
					<small><?php echo esc_html( $field_description ); ?></small>
				</div>
			<?php endif; ?>
		</div>
		<?php

		$html = ob_get_clean();

		// Hook after field.
		do_action( 'obsidian_forms_after_field', $this, $form_settings );
		do_action( 'obsidian_forms_after_field_' . $this->type, $this, $form_settings );

		return $html;
	}

	/**
	 * Render the input element. Can be overridden by child classes.
	 *
	 * @param string $placeholder Field placeholder.
	 *
	 * @return string The HTML for the input element.
	 */
	protected function render_input( string $placeholder = '' ): string {
		return '<input type="' . esc_attr( $this->get_input_type() ) . '" id="' . esc_attr( $this->name ) . '" name="' . esc_attr( $this->name ) . '" value="' . esc_attr( $this->value ) . '" placeholder="' . esc_attr( $placeholder ) . '" class="wp-block-obsidian-form-field__input" />';
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
		$this->errors = [];

		foreach ( $this->validation_rules as $rule ) {
			if ( 'required' === $rule && ( '' === $value || [] === $value ) ) {
				$this->add_error( __( 'This field is required.', 'obsidian-forms' ) );

				return false;
			}
		}

		// Allow custom validation via filter.
		$is_valid = apply_filters( 'obsidian_forms_validate_field', true, $value, $this );
		$is_valid = apply_filters( 'obsidian_forms_validate_field_' . $this->type, $is_valid, $value, $this );

		return $is_valid;
	}

	/**
	 * Add a validation error.
	 *
	 * @param string $error Error message.
	 *
	 * @return void
	 */
	public function add_error( string $error ): void {
		$this->errors[] = $error;
	}

	/**
	 * Get validation errors.
	 *
	 * @return array
	 */
	public function get_errors(): array {
		return $this->errors;
	}

	/**
	 * Check if field has errors.
	 *
	 * @return bool
	 */
	public function has_errors(): bool {
		return ! empty( $this->errors );
	}

	/**
	 * Sanitize the field value.
	 * Can be overridden by child classes for type-specific sanitization.
	 *
	 * @param mixed $value The value to sanitize.
	 *
	 * @return mixed The sanitized value.
	 */
	public function sanitize( $value ) {
		// Default sanitization for text fields.
		if ( is_array( $value ) ) {
			return array_map( 'sanitize_text_field', $value );
		}

		return sanitize_text_field( $value );
	}
}
