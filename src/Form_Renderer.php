<?php

namespace Obsidian_Forms;

use Obsidian_Forms\Models\Form;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the rendering of forms on the frontend.
 *
 * @since   0.1.0
 * @version 0.1.0
 */
class Form_Renderer {

	/**
	 * The Form model instance.
	 *
	 * @var Form|null
	 */
	protected ?Form $form = null;

	/**
	 * Form settings.
	 *
	 * @var array
	 */
	protected array $form_settings = [];

	/**
	 * Current field index (tracks position across all field groups).
	 *
	 * @var int
	 */
	protected int $current_field_index = 0;

	/**
	 * Render a form by ID.
	 *
	 * @param int $form_id The form post ID.
	 *
	 * @return string The rendered form HTML.
	 */
	public function render_form( int $form_id ): string {
		if ( empty( $form_id ) ) {
			return '';
		}

		// Create form model instance.
		$this->form = new Form( $form_id );

		if ( empty( $this->form->get_form_id() ) ) {
			return '';
		}

		// Enqueue block styles since we're bypassing WordPress block rendering.
		$this->enqueue_block_styles();

		// Get form settings.
		$this->form_settings = $this->form->get_settings();

		// Reset field index.
		$this->current_field_index = 0;

		// Hook before form.
		do_action( 'obsidian_forms_before_form', $this->form, $this->form_settings );

		ob_start();

		// Get form post to access blocks.
		$form_post = get_post( $form_id );

		if ( ! $form_post ) {
			return '';
		}

		// Parse blocks.
		$blocks = parse_blocks( $form_post->post_content );

		// Build form classes.
		$form_classes = $this->get_form_classes();

		// Get form attributes.
		$form_attributes = $this->get_form_attributes();

		?>
		<form <?php echo wp_kses_data( $form_attributes ); ?>>
			<?php
			// Hook after form open tag.
			do_action( 'obsidian_forms_after_form_open', $this->form, $this->form_settings );

			// Add nonce field for POST submissions.
			wp_nonce_field( 'obsidian_form_submit_' . $form_id, 'obsidian_form_nonce' );

			// Add REST nonce field for AJAX submissions.
			echo '<input type="hidden" id="obsidian_form_rest_nonce" name="obsidian_form_rest_nonce" value="' . esc_attr( wp_create_nonce( 'wp_rest' ) ) . '" />';

			// Add hidden form ID field.
			echo '<input type="hidden" name="obsidian_form_id" value="' . esc_attr( $form_id ) . '" />';

			// Render success message container.
			$this->render_message_container();

			// Render field groups.
			$this->render_field_groups( $blocks );

			// Render submit button.
			$this->render_submit_button();

			// Hook before form close tag.
			do_action( 'obsidian_forms_before_form_close', $this->form, $this->form_settings );
			?>
		</form>
		<?php

		$html = ob_get_clean();

		// Hook after form.
		do_action( 'obsidian_forms_after_form', $this->form, $this->form_settings );

		return $html;
	}

	/**
	 * Render field groups.
	 *
	 * @param array $blocks The parsed blocks.
	 *
	 * @return void
	 */
	protected function render_field_groups( array $blocks ): void {
		foreach ( $blocks as $block ) {
			if ( 'obsidian-form/field-group' !== $block['blockName'] ) {
				continue;
			}

			// Hook before field group.
			do_action( 'obsidian_forms_before_field_group', $block, $this->form, $this->form_settings );

			// Get field group classes.
			$field_group_classes = [ 'wp-block-obsidian-form-field-group' ];

			// Calculate flex width based on number of fields.
			$field_count = count( $block['innerBlocks'] ?? [] );
			$flex_width  = '';

			if ( $field_count > 1 ) {
				$flex_width = ' style="--of-field-flex: ' . ( 100 / $field_count ) . '%;"';
			}

			echo '<div class="' . esc_attr( implode( ' ', $field_group_classes ) ) . '"' . $flex_width . '>';

			// Render fields in this group.
			if ( ! empty( $block['innerBlocks'] ) ) {
				$this->render_fields( $block['innerBlocks'] );
			}

			echo '</div>';

			// Hook after field group.
			do_action( 'obsidian_forms_after_field_group', $block, $this->form, $this->form_settings );
		}
	}

	/**
	 * Render fields.
	 *
	 * @param array $field_blocks The field blocks.
	 *
	 * @return void
	 */
	protected function render_fields( array $field_blocks ): void {
		$fields = $this->form->get_fields();

		foreach ( $field_blocks as $field_block ) {
			if ( 'obsidian-form/field' !== $field_block['blockName'] ) {
				continue;
			}

			// Find the corresponding field model using global index.
			if ( isset( $fields[ $this->current_field_index ] ) ) {
				$this->render_field( $fields[ $this->current_field_index ] );
				++$this->current_field_index;
			}
		}
	}

	/**
	 * Render a single field using its model.
	 *
	 * @param \Obsidian_Forms\Models\Field $field The field model.
	 *
	 * @return void
	 */
	protected function render_field( $field ): void {
		if ( ! $field ) {
			return;
		}

		echo $field->render( $this->form_settings );
	}

	/**
	 * Get form CSS classes.
	 *
	 * @return array
	 */
	protected function get_form_classes(): array {
		$classes = [
			'wp-block-obsidian-form-form',
			'obsidian-form-' . $this->form->get_form_id(),
		];

		return apply_filters( 'obsidian_forms_form_classes', $classes, $this->form );
	}

	/**
	 * Get field CSS classes.
	 *
	 * @param \Obsidian_Forms\Models\Field $field The field model.
	 *
	 * @return array
	 */
	protected function get_field_classes( $field ): array {
		$attributes = $field->get_attributes();

		$classes = [
			'wp-block-obsidian-form-field',
			'obsidian-forms-field__' . ( $attributes['fieldType'] ?? 'text' ),
		];

		if ( ! empty( $attributes['isRequired'] ) ) {
			$classes[] = 'obsidian-forms-field__required';
		}

		if ( ! empty( $attributes['fieldName'] ) ) {
			$classes[] = 'obsidian-forms-field__name-' . sanitize_html_class( $attributes['fieldName'] );
		}

		return apply_filters( 'obsidian_forms_field_classes', $classes, $field );
	}

	/**
	 * Get form attributes for the form tag.
	 *
	 * @return string
	 */
	protected function get_form_attributes(): string {
		$classes = $this->get_form_classes();
		$form_id = $this->form->get_form_id();

		$attributes = [
			'class'        => implode( ' ', $classes ),
			'method'       => 'post',
			'action'       => esc_url( $_SERVER['REQUEST_URI'] ?? '' ),
			'novalidate'   => true,
			'data-form-id' => $form_id,
			'data-ajax'    => 'true',
		];

		$attributes = apply_filters( 'obsidian_forms_form_attributes', $attributes, $this->form );

		// Build attribute string.
		$attr_string = '';

		foreach ( $attributes as $key => $value ) {
			if ( is_bool( $value ) ) {
				if ( $value ) {
					$attr_string .= ' ' . esc_attr( $key );
				}
			} else {
				$attr_string .= ' ' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
			}
		}

		return trim( $attr_string );
	}

	/**
	 * Render the submit button.
	 *
	 * @return void
	 */
	protected function render_submit_button(): void {
		$button_text = $this->form_settings['submitButtonText'] ?? __( 'Submit', 'obsidian-forms' );

		$button_html  = '<div class="wp-block-obsidian-form-field-group wp-block-obsidian-form-field-group--submit">';
		$button_html .= '<div class="wp-block-obsidian-form-field wp-block-obsidian-form-field--submit">';
		$button_html .= '<button type="submit" id="obsidian-form-submit" class="obsidian-form-submit-button">' . esc_html( $button_text ) . '</button>';
		$button_html .= '</div>';
		$button_html .= '</div>';

		// Allow filtering of submit button.
		$button_html = apply_filters( 'obsidian_forms_submit_button', $button_html, $this->form, $this->form_settings );

		echo $button_html;
	}

	/**
	 * Enqueue block styles and localize scripts.
	 *
	 * Note: The view.js script is auto-enqueued by WordPress via block.json.
	 * We only need to manually enqueue the styles and localize the script.
	 *
	 * @return void
	 */
	protected function enqueue_block_styles(): void {
		// Enqueue field-group styles.
		$field_group_style = OBSIDIAN_FORMS_PATH . 'blocks/build/field-group/style-index.css';
		if ( file_exists( $field_group_style ) ) {
			wp_enqueue_style(
				'obsidian-form-field-group-style',
				OBSIDIAN_FORMS_URL . 'blocks/build/field-group/style-index.css',
				[],
				filemtime( $field_group_style )
			);
		}

		// Enqueue field styles.
		$field_style = OBSIDIAN_FORMS_PATH . 'blocks/build/field/style-index.css';
		if ( file_exists( $field_style ) ) {
			wp_enqueue_style(
				'obsidian-form-field-style',
				OBSIDIAN_FORMS_URL . 'blocks/build/field/style-index.css',
				[],
				filemtime( $field_style )
			);
		}

		// The form view script is auto-enqueued by WordPress via block.json.
		// No localization needed - the script reads the nonce from the hidden field in the form.
	}

	/**
	 * Render the message container for success/error messages.
	 *
	 * @return void
	 */
	protected function render_message_container(): void {
		$form_id = $this->form->get_form_id();

		// Check if there's a success message to display.
		if ( isset( $_GET['obsidian_form_success'] ) && (int) $_GET['obsidian_form_success'] === $form_id ) {
			$success_message = $this->form_settings['successMessage'] ?? __( 'Thank you! Your form has been submitted successfully.', 'obsidian-forms' );
			$success_message = apply_filters( 'obsidian_forms_success_message', $success_message, $this->form );

			echo '<div class="obsidian-form-message obsidian-form-message--success" role="alert">';
			echo '<p>' . esc_html( $success_message ) . '</p>';
			echo '</div>';
		}

		// Check if there are validation errors to display.
		if ( isset( $_GET['obsidian_form_error'] ) && (int) $_GET['obsidian_form_error'] === $form_id ) {
			$error_message = $this->form_settings['errorMessage'] ?? __( 'There was an error submitting your form. Please check the fields and try again.', 'obsidian-forms' );
			$error_message = apply_filters( 'obsidian_forms_error_message', $error_message, $this->form );

			echo '<div class="obsidian-form-message obsidian-form-message--error" role="alert">';
			echo '<p>' . esc_html( $error_message ) . '</p>';
			echo '</div>';
		}

		// Container for AJAX messages.
		echo '<div class="obsidian-form-message-container" role="alert" aria-live="polite"></div>';
	}
}
