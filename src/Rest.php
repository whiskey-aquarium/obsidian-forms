<?php

namespace Obsidian_Forms;

// Exit if accessed directly.
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
		add_filter( 'rest_pre_dispatch', [ $this, 'authorize_form_routes' ], 10, 3 );
		add_action( 'rest_api_init', [ $this, 'register_form_settings_endpoints' ] );
	}

	/**
	 * Restricts every core REST route for the private form-definition post type.
	 *
	 * Core exposes published custom post types through REST even when
	 * publicly_queryable is false, so the post type capabilities alone do not
	 * protect form schemas.
	 *
	 * @param mixed            $result  Response to replace, if one exists.
	 * @param \WP_REST_Server  $server  REST server instance.
	 * @param \WP_REST_Request $request Current request.
	 * @return mixed
	 */
	public function authorize_form_routes( $result, $server, $request ) {
		unset( $server );

		if ( 0 !== strpos( $request->get_route(), '/wp/v2/obsidian_form' ) ) {
			return $result;
		}

		if ( current_user_can( 'manage_options' ) ) {
			return $result;
		}

		return new \WP_Error(
			'obsidian_forms_rest_forbidden',
			__( 'You are not allowed to access form definitions.', 'obsidian-forms' ),
			[ 'status' => rest_authorization_required_code() ]
		);
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
					return current_user_can( 'manage_options' );
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
}
