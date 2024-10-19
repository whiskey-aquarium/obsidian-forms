<?php

namespace Obsidian_Forms;

// Exit if accessed directly.
use WP_REST_Request;

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
