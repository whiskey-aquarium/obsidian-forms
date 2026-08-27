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
		add_action( 'rest_api_init', [ $this, 'register_submission_endpoint' ] );
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
				'callback'            => [ $this, 'get_form_settings' ],
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

		return new WP_REST_Response(
			[
				'metadata' => $form->get_form_settings_metadata(),
				'defaults' => $form->get_default_settings(),
			]
		);
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

	/**
	 * Register the form submission REST endpoint.
	 *
	 * @return void
	 */
	public function register_submission_endpoint(): void {
		register_rest_route(
			'obsidian-forms/v1',
			'/submit',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'handle_form_submission' ],
				'permission_callback' => '__return_true', // Allow public submissions.
			]
		);
	}

	/**
	 * Handle form submission via REST API.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response
	 */
	public function handle_form_submission( WP_REST_Request $request ): WP_REST_Response {
		// Get form data from request.
		$params  = $request->get_params();
		$form_id = absint( $params['obsidian_form_id'] ?? 0 );

		// Note: Nonce verification is handled automatically by WordPress via the X-WP-Nonce header.
		// The JavaScript sends the REST nonce in the header, which WordPress validates.

		// Process submission.
		$submission = new Form_Submission();
		$result     = $submission->process_submission( $form_id, $params );

		// Handle result.
		if ( is_wp_error( $result ) ) {
			$error_data = $result->get_error_data();

			return new WP_REST_Response(
				[
					'success' => false,
					'data'    => [
						'message' => $result->get_error_message(),
						'errors'  => is_array( $error_data ) ? $error_data : [],
					],
				],
				400
			);
		}

		// Get form settings for success message and redirect.
		$form     = new Form( $form_id );
		$settings = $form->get_settings();

		$success_message = $settings['successMessage'] ?? __( 'Thank you! Your form has been submitted successfully.', 'obsidian-forms' );
		$redirect_url    = $settings['redirectUrl'] ?? '';

		$response_data = [
			'success' => true,
			'data'    => [
				'message'  => $success_message,
				'entry_id' => $result,
			],
		];

		if ( ! empty( $redirect_url ) ) {
			$response_data['data']['redirect_url'] = esc_url_raw( $redirect_url );
		}

		return new WP_REST_Response( $response_data, 200 );
	}
}
