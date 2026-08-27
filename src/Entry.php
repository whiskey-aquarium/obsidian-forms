<?php

namespace Obsidian_Forms;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stores submissions and provides the private Entries admin screens.
 */
final class Entry {
	/**
	 * Registers the entry post type and admin integrations.
	 *
	 * @return void
	 */
	public function initialize(): void {
		add_action( 'init', [ $this, 'register_post_type' ] );
		add_action( 'admin_menu', [ $this, 'add_menu' ], 20 );
		add_action( 'add_meta_boxes_obsidian_entry', [ $this, 'configure_edit_screen' ] );
		add_action( 'restrict_manage_posts', [ $this, 'render_form_filter' ] );
		add_action( 'pre_get_posts', [ $this, 'filter_entries' ] );
		add_filter( 'manage_obsidian_entry_posts_columns', [ $this, 'columns' ] );
		add_action( 'manage_obsidian_entry_posts_custom_column', [ $this, 'render_column' ], 10, 2 );
		add_filter( 'post_row_actions', [ $this, 'row_actions' ], 10, 2 );
		add_filter( 'bulk_actions-edit-obsidian_entry', [ $this, 'bulk_actions' ] );
		add_filter( 'use_block_editor_for_post_type', [ $this, 'disable_block_editor' ], 10, 2 );
	}

	/**
	 * Stores one validated submission.
	 *
	 * @param int   $form_id Source form ID.
	 * @param array $fields  Form field definitions.
	 * @param array $values  Sanitized submitted values.
	 * @return int|\WP_Error Entry ID or error.
	 */
	public static function store( int $form_id, array $fields, array $values ) {
		$payload = [];

		foreach ( $fields as $field ) {
			$name = sanitize_key( $field['fieldName'] ?? '' );

			if ( ! $name || ! array_key_exists( $name, $values ) ) {
				continue;
			}

			$payload[] = [
				'name'  => $name,
				'label' => sanitize_text_field( $field['fieldLabel'] ?? $name ),
				'type'  => sanitize_key( $field['fieldType'] ?? 'text' ),
				'value' => $values[ $name ],
			];
		}

		return wp_insert_post(
			[
				'post_type'    => 'obsidian_entry',
				'post_status'  => 'private',
				'post_parent'  => $form_id,
				'post_title'   => sprintf(
					/* translators: 1: form title, 2: submission date and time. */
					__( '%1$s — %2$s', 'obsidian-forms' ),
					get_the_title( $form_id ),
					current_time( 'mysql' )
				),
				'post_content' => wp_slash( wp_json_encode( $payload ) ),
				'meta_input'   => [
					'_obsidian_form_id'      => $form_id,
					'_obsidian_email_status' => 'pending',
				],
			],
			true
		);
	}

	/**
	 * Registers the private entry post type.
	 *
	 * @return void
	 */
	public function register_post_type(): void {
		register_post_type(
			'obsidian_entry',
			[
				'labels'              => [
					'name'               => __( 'Entries', 'obsidian-forms' ),
					'singular_name'      => __( 'Entry', 'obsidian-forms' ),
					'all_items'          => __( 'Entries', 'obsidian-forms' ),
					'edit_item'          => __( 'View Entry', 'obsidian-forms' ),
					'search_items'       => __( 'Search Entries', 'obsidian-forms' ),
					'not_found'          => __( 'No entries found.', 'obsidian-forms' ),
					'not_found_in_trash' => __( 'No entries found in Trash.', 'obsidian-forms' ),
				],
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => false,
				'show_in_rest'        => false,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'supports'            => false,
				'capabilities'        => [
					'edit_post'           => 'manage_options',
					'read_post'           => 'manage_options',
					'delete_post'         => 'manage_options',
					'edit_posts'          => 'manage_options',
					'edit_others_posts'   => 'manage_options',
					'delete_posts'        => 'manage_options',
					'delete_others_posts' => 'manage_options',
					'publish_posts'       => 'do_not_allow',
					'read_private_posts'  => 'manage_options',
					'create_posts'        => 'do_not_allow',
				],
			]
		);
	}

	/**
	 * Adds Entries beneath the plugin menu.
	 *
	 * @return void
	 */
	public function add_menu(): void {
		add_submenu_page(
			'obsidian-forms',
			__( 'Entries', 'obsidian-forms' ),
			__( 'Entries', 'obsidian-forms' ),
			'manage_options',
			'edit.php?post_type=obsidian_entry'
		);
	}

	/**
	 * Configures the read-only entry detail screen.
	 *
	 * @return void
	 */
	public function configure_edit_screen(): void {
		remove_meta_box( 'submitdiv', 'obsidian_entry', 'side' );
		add_meta_box(
			'obsidian-entry-details',
			__( 'Submission Details', 'obsidian-forms' ),
			[ $this, 'render_details' ],
			'obsidian_entry',
			'normal',
			'high'
		);
	}

	/**
	 * Renders safely escaped entry details.
	 *
	 * @param \WP_Post $post Entry post.
	 * @return void
	 */
	public function render_details( \WP_Post $post ): void {
		$form    = get_post( $post->post_parent );
		$payload = json_decode( $post->post_content, true );
		$payload = is_array( $payload ) ? $payload : [];
		?>
		<p>
			<strong><?php esc_html_e( 'Form:', 'obsidian-forms' ); ?></strong>
			<?php if ( $form instanceof \WP_Post && current_user_can( 'edit_post', $form->ID ) ) : ?>
				<a href="<?php echo esc_url( get_edit_post_link( $form->ID ) ); ?>"><?php echo esc_html( get_the_title( $form ) ); ?></a>
			<?php else : ?>
				<?php echo esc_html( $form instanceof \WP_Post ? get_the_title( $form ) : __( 'Deleted form', 'obsidian-forms' ) ); ?>
			<?php endif; ?>
		</p>
		<p><strong><?php esc_html_e( 'Submitted:', 'obsidian-forms' ); ?></strong> <?php echo esc_html( get_the_date( 'F j, Y g:i a', $post ) ); ?></p>
		<table class="widefat striped">
			<tbody>
			<?php foreach ( $payload as $field ) : ?>
				<tr>
					<th scope="row" style="width:25%"><?php echo esc_html( $field['label'] ?? $field['name'] ?? '' ); ?></th>
					<td><?php echo nl2br( esc_html( is_array( $field['value'] ?? '' ) ? implode( ', ', $field['value'] ) : $field['value'] ?? '' ) ); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Defines entry list columns.
	 *
	 * @param array $columns Existing columns.
	 * @return array
	 */
	public function columns( array $columns ): array {
		return [
			'cb'             => $columns['cb'],
			'obsidian_entry' => __( 'Entry', 'obsidian-forms' ),
			'obsidian_form'  => __( 'Form', 'obsidian-forms' ),
			'email_status'   => __( 'Email', 'obsidian-forms' ),
			'submitted'      => __( 'Submitted', 'obsidian-forms' ),
		];
	}

	/**
	 * Renders entry list column values.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Entry ID.
	 * @return void
	 */
	public function render_column( string $column, int $post_id ): void {
		if ( 'obsidian_entry' === $column ) {
			printf(
				'<strong><a href="%1$s">%2$s</a></strong>',
				esc_url( get_edit_post_link( $post_id ) ),
				/* translators: %d: entry ID. */
				esc_html( sprintf( __( 'Entry #%d', 'obsidian-forms' ), $post_id ) )
			);
		}

		if ( 'obsidian_form' === $column ) {
			$form_id = (int) wp_get_post_parent_id( $post_id );
			$title   = $form_id ? get_the_title( $form_id ) : '';
			echo $form_id ? esc_html( $title ? $title : __( 'Deleted form', 'obsidian-forms' ) ) : '&mdash;';
		}

		if ( 'email_status' === $column ) {
			$status = sanitize_key( get_post_meta( $post_id, '_obsidian_email_status', true ) );
			$labels = [
				'sent'     => __( 'Sent', 'obsidian-forms' ),
				'failed'   => __( 'Failed', 'obsidian-forms' ),
				'disabled' => __( 'Disabled', 'obsidian-forms' ),
				'pending'  => __( 'Pending', 'obsidian-forms' ),
			];
			echo esc_html( $labels[ $status ] ?? __( 'Unknown', 'obsidian-forms' ) );
		}

		if ( 'submitted' === $column ) {
			echo esc_html( get_the_date( 'F j, Y g:i a', $post_id ) );
		}
	}

	/**
	 * Replaces Edit wording with View for entries.
	 *
	 * @param array    $actions Row actions.
	 * @param \WP_Post $post    Current post.
	 * @return array
	 */
	public function row_actions( array $actions, \WP_Post $post ): array {
		if ( 'obsidian_entry' === $post->post_type && isset( $actions['edit'] ) ) {
			$actions['edit'] = sprintf( '<a href="%s">%s</a>', esc_url( get_edit_post_link( $post ) ), esc_html__( 'View', 'obsidian-forms' ) );
			unset( $actions['inline hide-if-no-js'] );
			unset( $actions['inline'] );
		}

		return $actions;
	}

	/**
	 * Removes bulk editing while retaining bulk trash actions.
	 *
	 * @param array $actions Bulk actions.
	 * @return array
	 */
	public function bulk_actions( array $actions ): array {
		unset( $actions['edit'] );

		return $actions;
	}

	/**
	 * Renders a source-form filter on the entry list.
	 *
	 * @param string $post_type Current post type.
	 * @return void
	 */
	public function render_form_filter( string $post_type ): void {
		if ( 'obsidian_entry' !== $post_type ) {
			return;
		}

		$selected = absint( $_GET['obsidian_form_id'] ?? 0 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only list filter.
		$forms    = get_posts(
			[
				'post_type'      => 'obsidian_form',
				'post_status'    => [ 'publish', 'draft', 'private' ],
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			]
		);
		?>
		<label class="screen-reader-text" for="obsidian-form-filter"><?php esc_html_e( 'Filter by form', 'obsidian-forms' ); ?></label>
		<select id="obsidian-form-filter" name="obsidian_form_id">
			<option value=""><?php esc_html_e( 'All forms', 'obsidian-forms' ); ?></option>
			<?php foreach ( $forms as $form ) : ?>
				<option value="<?php echo esc_attr( $form->ID ); ?>" <?php selected( $selected, $form->ID ); ?>><?php echo esc_html( get_the_title( $form ) ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Applies the source-form entry filter.
	 *
	 * @param \WP_Query $query Current query.
	 * @return void
	 */
	public function filter_entries( \WP_Query $query ): void {
		if ( ! is_admin() || ! $query->is_main_query() || 'obsidian_entry' !== $query->get( 'post_type' ) ) {
			return;
		}

		$form_id = absint( $_GET['obsidian_form_id'] ?? 0 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only list filter.

		if ( $form_id ) {
			$query->set( 'post_parent', $form_id );
		}
	}

	/**
	 * Uses the classic read-only screen for entries.
	 *
	 * @param bool   $use_block_editor Current decision.
	 * @param string $post_type        Post type.
	 * @return bool
	 */
	public function disable_block_editor( bool $use_block_editor, string $post_type ): bool {
		return 'obsidian_entry' === $post_type ? false : $use_block_editor;
	}
}
