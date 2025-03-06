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

		if ( empty( $form ) ) {
			return;
		}

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
			'labelPlacement' => [
				'label' => __('Field Label Placement', 'obsidian-forms'),
				'type' => 'select',
				'default' => 'top',
				'options' => [
					['label' => __('Top', 'obsidian-forms'), 'value' => 'top'],
					['label' => __('Left', 'obsidian-forms'), 'value' => 'left'],
					['label' => __('Bottom', 'obsidian-forms'), 'value' => 'bottom']
				]
			],
			'descriptionPlacement' => [
				'label' => __('Field Description Placement', 'obsidian-forms'),
				'type' => 'radio',
				'default' => 'bottom',
				'options' => [
					['label' => __('Top', 'obsidian-forms'), 'value' => 'top'],
					['label' => __('Bottom', 'obsidian-forms'), 'value' => 'bottom']
				]
			],
			'globalHasPlaceholder' => [
				'label' => __('Fields Have Placeholders', 'obsidian-forms'),
				'type' => 'toggle',
				'default' => true
			],
			'validationPlacement' => [
				'label' => __('Validation Placement', 'obsidian-forms'),
				'type' => 'radio',
				'default' => 'bottom',
				'options' => [
					['label' => __('Top', 'obsidian-forms'), 'value' => 'top'],
					['label' => __('Bottom', 'obsidian-forms'), 'value' => 'bottom']
				]
			],
			'requiredIndicator' => [
				'label' => __('Required Indicator', 'obsidian-forms'),
				'type' => 'string',
				'default' => '*'
			],
		];

		return apply_filters('obsidian_forms_settings_metadata', $settings);
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

		foreach ($metadata as $key => $data) {
			$settings[$key] = [
				'label' => $data['label'],
				'type' => $data['type'],
				'value' => $data['default']
			];

			if (isset($data['options'])) {
				$settings[$key]['options'] = $data['options'];
			}
		}

		return apply_filters('obsidian_forms_form_settings', $settings);
	}

	/**
	 * Returns the default values for all form settings.
	 *
	 * @return array
	 */
	public function get_default_settings(): array {
		$metadata = $this->get_form_settings_metadata();
		$defaults = [];

		foreach ($metadata as $key => $data) {
			$defaults[$key] = $data['default'];
		}

		return apply_filters('obsidian_forms_default_settings', $defaults);
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
			'template'           => [
				[
					'obsidian-form/field-group',
					[],
					[
						[
							'obsidian-form/field',
							[
								'isRequired' => true
							]
						]
					]
				]
			],
		];

		return apply_filters( 'obsidian_forms_post_type_args', $args );
	}

	private function setup_form( $form ) {
		// TODO: Get the form fields from the post and set $this->fields.
	}
}
