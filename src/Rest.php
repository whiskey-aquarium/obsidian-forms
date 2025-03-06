<?php

namespace Obsidian_Forms;

// Exit if accessed directly.
use WP_REST_Request;
use WP_REST_Response;
use Obsidian_Forms\Models\Form;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Rest {
	/**
	 * Initializes the admin.
	 *
	 * @return  void
	 * @version 0.1.0
	 *
	 * @since   0.1.0
	 */
	public function initialize(): void {
		add_action( 'rest_api_init', [ $this, 'add_raw_content_to_rest_response' ] );
		add_action( 'rest_api_init', [ $this, 'register_form_settings_endpoints' ] );
	}

	/**
	 * Registers the form settings REST endpoints.
	 *
	 * @return void
	 */
	public function register_form_settings_endpoints(): void {
		register_rest_route(
			'obsidian-forms/v1',
			'/form-settings',
			[
				'methods'             => 'GET',
				'callback'           => [ $this, 'get_form_settings' ],
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			]
		);
	}

	/**
	 * Gets the form settings metadata.
	 *
	 * @return WP_REST_Response
	 */
	public function get_form_settings(): WP_REST_Response {
		$form = new Form();
		
		return new WP_REST_Response([
			'metadata' => $form->get_form_settings_metadata(),
			'defaults' => $form->get_default_settings()
		]);
	}

	/**
	 * Adds the raw content to the rest response for the obsidian_form post type.
	 *
	 * @return void
	 * @version 0.1.0
	 *
	 * @since   0.1.0
	 */
	public function add_raw_content_to_rest_response() {
		register_rest_field(
			'obsidian_form',
			'raw_content',
			[
				'get_callback'    => [ $this, 'get_raw_content' ],
				'update_callback' => null,
				'schema'          => null,
			]
		);
	}

	/**
	 * Gets the raw content for the obsidian_form post type.
	 *
	 * @param array $rest_object The object.
	 *
	 * @version 0.1.0
	 * @since   0.1.0
	 */
	public function get_raw_content( array $rest_object ): string {
		return $rest_object['content']['raw'] ?? '';
	}
}
