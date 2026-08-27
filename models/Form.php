<?php

namespace Obsidian_Forms\Models;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form model to represent form.
 *
 * @since   0.1.0
 * @version 0.1.0
 */
class Form {
	/**
	 * The form's fields.
	 *
	 * @var array
	 */
	public array $fields = [];

	/**
	 * The form ID.
	 *
	 * @var int
	 */
	protected int $form_id = 0;

	/**
	 * The form post object.
	 *
	 * @var \WP_Post|null
	 */
	protected ?\WP_Post $post = null;

	/**
	 * The form settings.
	 *
	 * @var array
	 */
	protected array $settings = [];

	/**
	 * The post type args.
	 *
	 * @var array
	 */
	public array $post_type_args;

	/**
	 * Constructor.
	 *
	 * @param int $id The id of the form (optional).
	 */
	public function __construct( int $id = 0 ) {
		$this->post_type_args = $this->get_post_type_args();

		if ( empty( $id ) ) {
			return;
		}

		$form = get_post( $id );

		if ( ! $form instanceof \WP_Post || 'obsidian_form' !== $form->post_type ) {
			return;
		}

		$this->form_id = $id;
		$this->post    = $form;
		$this->setup_form( $form );
	}

	/**
	 * Returns the form fields.
	 *
	 * @return array
	 */
	public function get_fields(): array {
		return $this->fields;
	}

	/**
	 * Returns the complete form settings metadata including types, options, and defaults.
	 * This is the single source of truth for form settings.
	 *
	 * @return array
	 */
	public function get_form_settings_metadata(): array {
		$settings = [
			'descriptionPlacement'      => [
				'label'   => __( 'Field Description Placement', 'obsidian-forms' ),
				'type'    => 'radio',
				'default' => 'bottom',
				'options' => [
					[
						'label' => __( 'Top', 'obsidian-forms' ),
						'value' => 'top',
					],
					[
						'label' => __( 'Bottom', 'obsidian-forms' ),
						'value' => 'bottom',
					],
				],
			],
			'globalHasPlaceholder'      => [
				'label'   => __( 'Fields Have Placeholders', 'obsidian-forms' ),
				'type'    => 'toggle',
				'default' => true,
			],
			'requiredIndicator'         => [
				'label'   => __( 'Required Indicator', 'obsidian-forms' ),
				'type'    => 'string',
				'default' => '*',
			],
			'submitButtonText'          => [
				'label'   => __( 'Submit Button Text', 'obsidian-forms' ),
				'type'    => 'string',
				'default' => __( 'Submit', 'obsidian-forms' ),
			],
			'successMessage'            => [
				'label'   => __( 'Success Message', 'obsidian-forms' ),
				'type'    => 'textarea',
				'default' => __( 'Thank you! Your form has been submitted successfully.', 'obsidian-forms' ),
			],
			'errorMessage'              => [
				'label'   => __( 'Error Message', 'obsidian-forms' ),
				'type'    => 'textarea',
				'default' => __( 'There was an error submitting your form. Please check the fields and try again.', 'obsidian-forms' ),
			],
			'redirectUrl'               => [
				'label'   => __( 'Redirect URL', 'obsidian-forms' ),
				'type'    => 'url',
				'default' => '',
			],
			'emailNotificationsEnabled' => [
				'label'   => __( 'Enable Email Notifications', 'obsidian-forms' ),
				'type'    => 'toggle',
				'default' => true,
			],
			'emailRecipient'            => [
				'label'   => __( 'Email Recipient', 'obsidian-forms' ),
				'type'    => 'email',
				'default' => get_option( 'admin_email' ),
			],
			'emailSubject'              => [
				'label'   => __( 'Email Subject', 'obsidian-forms' ),
				'type'    => 'string',
				'default' => __( 'New form submission: {form_title}', 'obsidian-forms' ),
			],
			'emailMessage'              => [
				'label'   => __( 'Email Message', 'obsidian-forms' ),
				'type'    => 'textarea',
				'default' => '',
			],
		];

		return apply_filters( 'obsidian_forms_settings_metadata', $settings );
	}

	/**
	 * Returns the settings for the form block.
	 * This method now uses get_form_settings_metadata() as its source of truth.
	 *
	 * @return array
	 */
	public function get_form_settings(): array {
		$metadata = $this->get_form_settings_metadata();
		$settings = [];

		foreach ( $metadata as $key => $data ) {
			$settings[ $key ] = [
				'label' => $data['label'],
				'type'  => $data['type'],
				'value' => $data['default'],
			];

			if ( isset( $data['options'] ) ) {
				$settings[ $key ]['options'] = $data['options'];
			}
		}

		return apply_filters( 'obsidian_forms_form_settings', $settings );
	}

	/**
	 * Returns the default values for all form settings.
	 *
	 * @return array
	 */
	public function get_default_settings(): array {
		$metadata = $this->get_form_settings_metadata();
		$defaults = [];

		foreach ( $metadata as $key => $data ) {
			$defaults[ $key ] = $data['default'];
		}

		return apply_filters( 'obsidian_forms_default_settings', $defaults );
	}

	/**
	 * Get the CPT args.
	 *
	 * @return array
	 */
	protected function get_post_type_args(): array {
		$args = [
			'labels'             => [
				'name'                  => __( 'Forms', 'obsidian-forms' ),
				'singular_name'         => __( 'Form', 'obsidian-forms' ),
				'menu_name'             => __( 'Forms', 'obsidian-forms' ),
				'add_new'               => __( 'Add Form', 'obsidian-forms' ),
				'add_new_item'          => __( 'Add New Form', 'obsidian-forms' ),
				'edit_item'             => __( 'Edit Form', 'obsidian-forms' ),
				'new_item'              => __( 'New Form', 'obsidian-forms' ),
				'view_item'             => __( 'View Form', 'obsidian-forms' ),
				'search_items'          => __( 'Search Forms', 'obsidian-forms' ),
				'not_found'             => __( 'No forms found', 'obsidian-forms' ),
				'not_found_in_trash'    => __( 'No forms found in Trash', 'obsidian-forms' ),
				'all_items'             => __( 'All Forms', 'obsidian-forms' ),
				'archives'              => __( 'Form Archives', 'obsidian-forms' ),
				'insert_into_item'      => __( 'Insert into form', 'obsidian-forms' ),
				'uploaded_to_this_item' => __( 'Uploaded to this form', 'obsidian-forms' ),
				'filter_items_list'     => __( 'Filter forms list', 'obsidian-forms' ),
				'items_list_navigation' => __( 'Forms list navigation', 'obsidian-forms' ),
				'items_list'            => __( 'Forms list', 'obsidian-forms' ),
			],
			'public'             => false,
			'show_ui'            => true,
			'show_in_menu'       => false,
			'has_archive'        => false,
			'publicly_queryable' => false,
			'supports'           => [ 'title', 'editor', 'custom-fields' ],
			'show_in_rest'       => true,
			'capability_type'    => 'post',
			'capabilities'       => [
				'edit_post'          => 'manage_options',
				'read_post'          => 'manage_options',
				'delete_post'        => 'manage_options',
				'edit_posts'         => 'manage_options',
				'edit_others_posts'  => 'manage_options',
				'delete_posts'       => 'manage_options',
				'publish_posts'      => 'manage_options',
				'read_private_posts' => 'manage_options',
				'create_posts'       => 'manage_options',
			],
			'template'           => [
				[
					'obsidian-form/field-group',
					[],
					[
						[
							'obsidian-form/field',
							[
								'isRequired' => true,
							],
						],
					],
				],
			],
		];

		return apply_filters( 'obsidian_forms_post_type_args', $args );
	}

	/**
	 * Setup the form by parsing blocks and loading settings.
	 *
	 * @param \WP_Post $form The form post object.
	 *
	 * @return void
	 */
	private function setup_form( $form ): void {
		// Load form settings.
		$this->settings = $this->get_settings();

		// Parse blocks to field models.
		$this->fields = $this->parse_blocks_to_fields( $form );
	}

	/**
	 * Parse form blocks and convert to field model instances.
	 *
	 * @param \WP_Post $form The form post object.
	 *
	 * @return array Array of field model instances.
	 */
	protected function parse_blocks_to_fields( $form ): array {
		$fields = [];

		// Parse the post content into blocks.
		$blocks = parse_blocks( $form->post_content );

		if ( empty( $blocks ) ) {
			return $fields;
		}

		// Iterate through field groups.
		foreach ( $blocks as $block ) {
			if ( 'obsidian-form/field-group' !== $block['blockName'] ) {
				continue;
			}

			// Get inner blocks (fields).
			if ( empty( $block['innerBlocks'] ) ) {
				continue;
			}

			foreach ( $block['innerBlocks'] as $field_block ) {
				if ( 'obsidian-form/field' !== $field_block['blockName'] ) {
					continue;
				}

				$field = $this->create_field_from_block( $field_block );

				if ( $field ) {
					$fields[] = $field;
				}
			}
		}

		return $fields;
	}

	/**
	 * Create a field model instance from a block.
	 *
	 * @param array $block The block array.
	 *
	 * @return Field|null
	 */
	protected function create_field_from_block( array $block ): ?Field {
		$attrs = $block['attrs'] ?? [];

		$field_type  = $attrs['fieldType'] ?? 'text';
		$field_label = $attrs['fieldLabel'] ?? '';
		$field_name  = $attrs['fieldName'] ?? '';
		$field_value = '';

		// Build the field class name.
		$class_name = $this->get_field_class_name( $field_type );

		// If class doesn't exist, fall back to base Field class.
		if ( ! class_exists( $class_name ) ) {
			$field = new Field( $field_label, $field_name, $field_type, $field_value );
		} else {
			$field = new $class_name( $field_label, $field_name, $field_value );
		}

		// Add validation rules if required.
		if ( ! empty( $attrs['isRequired'] ) ) {
			$field->add_validation_rules( [ 'required' ] );
		}

		// Store all block attributes in the field.
		$field->set_attributes( $attrs );

		return $field;
	}

	/**
	 * Get the field class name based on field type.
	 *
	 * @param string $field_type The field type.
	 *
	 * @return string
	 */
	protected function get_field_class_name( string $field_type ): string {
		$type_map = [
			'text'     => 'Text',
			'email'    => 'Email',
			'textarea' => 'Textarea',
			'select'   => 'Select',
			'checkbox' => 'Checkbox',
			'radio'    => 'Radio',
			'tel'      => 'Tel',
			'url'      => 'Url',
			'number'   => 'Number',
			'hidden'   => 'Hidden',
		];

		$class_name = $type_map[ $field_type ] ?? 'Field';

		return 'Obsidian_Forms\\Models\\Fields\\' . $class_name;
	}

	/**
	 * Get form ID.
	 *
	 * @return int
	 */
	public function get_form_id(): int {
		return $this->form_id;
	}

	/**
	 * Get form title.
	 *
	 * @return string
	 */
	public function get_form_title(): string {
		if ( ! $this->post ) {
			return '';
		}

		return $this->post->post_title;
	}

	/**
	 * Get form settings, merging defaults with saved settings.
	 *
	 * @return array
	 */
	public function get_settings(): array {
		$defaults = $this->get_default_settings();

		if ( ! $this->form_id ) {
			return $defaults;
		}

		$saved_settings = get_post_meta( $this->form_id, '_obsidian_form_settings', true );

		if ( empty( $saved_settings ) || ! is_array( $saved_settings ) ) {
			return $defaults;
		}

		return wp_parse_args( $saved_settings, $defaults );
	}

	/**
	 * Render the form.
	 * This method is called by Form_Renderer.
	 *
	 * @return string The rendered form HTML.
	 */
	public function render(): string {
		if ( ! $this->form_id || ! $this->post ) {
			return '';
		}

		// The rendering logic is handled by Form_Renderer class.
		// This method exists for potential future use or custom rendering.
		$renderer = new \Obsidian_Forms\Form_Renderer();

		return $renderer->render_form( $this->form_id );
	}
}
