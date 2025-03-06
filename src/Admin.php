<?php

namespace Obsidian_Forms;

use Obsidian_Forms\Models\Form;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Admin {
	/**
	 * Initializes the admin.
	 *
	 * @return  void
	 * @version 0.1.0
	 *
	 * @since   0.1.0
	 */
	public function initialize(): void {
		add_action( 'init', [ $this, 'register_form_settings_meta' ] );
		add_action( 'admin_menu', [ $this, 'add_menu' ] );
		add_action( 'init', [ $this, 'register_form_post_type' ] );
		add_action( 'init', [ $this, 'inject_form_settings' ] );
	}

	/**
	 * Injects form settings into JavaScript when blocks are registered.
	 *
	 * @return void
	 */
	public function inject_form_settings(): void {
		$form = new Form();
		$settings = [
			'metadata' => $form->get_form_settings_metadata(),
			'defaults' => $form->get_default_settings(),
		];
		
		wp_register_script(
			'obsidian-forms-settings',
			'',
			['wp-blocks'],
			'0.1.0',
			true
		);

		wp_add_inline_script(
			'obsidian-forms-settings',
			sprintf(
				'window.obsidianForms = window.obsidianForms || {};' .
				'window.obsidianForms.settings = %s;',
				wp_json_encode($settings)
			),
			'before'
		);

		wp_enqueue_script('obsidian-forms-settings');
	}

	/**
	 * Registers the form settings meta.
	 *
	 * @return void
	 */
	public function register_form_settings_meta() {
		$form = new Form();
		$metadata = $form->get_form_settings_metadata();
		$properties = [];

		foreach ($metadata as $key => $data) {
			$schema = ['type' => $this->map_field_type_to_schema_type($data['type'])];
			
			if (isset($data['default'])) {
				$schema['default'] = $data['default'];
			}
			
			if (isset($data['options'])) {
				$schema['enum'] = array_column($data['options'], 'value');
			}
			
			$properties[$key] = $schema;
		}

		register_post_meta(
			'obsidian_form',
			'_obsidian_form_settings',
			[
				'single'        => true,
				'type'          => 'object',
				'show_in_rest'  => [
					'schema' => [
						'type'       => 'object',
						'properties' => $properties
					]
				],
				'auth_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			]
		);
	}

	/**
	 * Maps form field types to JSON schema types.
	 *
	 * @param string $field_type The field type from the form settings.
	 * @return string The corresponding JSON schema type.
	 */
	private function map_field_type_to_schema_type(string $field_type): string {
		$map = [
			'string' => 'string',
			'select' => 'string',
			'radio' => 'string',
			'toggle' => 'boolean'
		];

		return $map[$field_type] ?? 'string';
	}

	/**
	 * Adds the admin menu and submenus.
	 *
	 * @return  void
	 * @version 0.1.0
	 *
	 * @since   0.1.0
	 */
	public function add_menu(): void {
		add_menu_page(
			'Obsidian Forms',
			'Obsidian Forms',
			'manage_options',
			'obsidian-forms',
			[ $this, 'render_dashboard' ],
			$this->get_svg_icon(),
			20
		);

		add_submenu_page(
			'obsidian-forms',
			'Dashboard',
			'Dashboard',
			'manage_options',
			'obsidian-forms',
			[ $this, 'render_dashboard' ]
		);

		add_submenu_page(
			'obsidian-forms',
			'Forms',
			'Forms',
			'manage_options',
			'edit.php?post_type=obsidian_form'
		);

		add_submenu_page(
			'obsidian-forms',
			'Add New Form',
			'Add Form',
			'manage_options',
			'post-new.php?post_type=obsidian_form'
		);

		add_submenu_page(
			'obsidian-forms',
			'Obsidian Forms Settings',
			'Settings',
			'manage_options',
			'obsidian-forms-settings',
			[ $this, 'render_settings' ]
		);

		add_submenu_page(
			'obsidian-forms',
			'Obsidian Forms Add-ons',
			'<span style="color:#d454ff">' . esc_html__( 'Add-ons', 'obsidian-forms' ) . '</span>',
			'manage_options',
			'obsidian-forms-add-ons',
			[ $this, 'render_add_ons' ]
		);
	}

	/**
	 * Renders the Dashboard submenu.
	 *
	 * @return  void
	 * @version 0.1.0
	 *
	 * @since   0.1.0
	 */
	public function render_dashboard(): void {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Obsidian Forms Dashboard', 'obsidian-forms' ); ?></h1>
			<p><?php esc_html_e( 'Welcome to the Obsidian Forms plugin dashboard.', 'obsidian-forms' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Renders the Settings submenu.
	 *
	 * @return  void
	 * @version 0.1.0
	 *
	 * @since   0.1.0
	 */
	public function render_settings(): void {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Obsidian Forms Settings', 'obsidian-forms' ); ?></h1>
			<p><?php esc_html_e( 'Settings page description.', 'obsidian-forms' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Renders the Add-ons submenu.
	 *
	 * @return  void
	 * @version 0.1.0
	 *
	 * @since   0.1.0
	 */
	public function render_add_ons(): void {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Obsidian Forms Add-ons', 'obsidian-forms' ); ?></h1>
			<p><?php esc_html_e( 'Add-ons page description.', 'obsidian-forms' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Registers the form post type.
	 *
	 * @return void
	 * @since 0.1.0
	 *
	 */
	public function register_form_post_type() {
		$form = new Models\Form();
		register_post_type( 'obsidian_form', $form->post_type_args );
	}

	/**
	 * Retrieves the SVG icon as a base64 encoded string.
	 *
	 * @return  string
	 * @version 0.1.0
	 *
	 * @since   0.1.0
	 */
	private function get_svg_icon(): string {
		$svg_file = OBSIDIAN_FORMS_PATH . '/assets/images/icon.svg';

		if ( file_exists( $svg_file ) ) {
			$svg_contents = file_get_contents( $svg_file );
			$base64_svg   = base64_encode( $svg_contents );

			return 'data:image/svg+xml;base64,' . $base64_svg;
		}

		// Fallback to a dashicon if the SVG is missing.
		return 'dashicons-feedback';
	}
}
