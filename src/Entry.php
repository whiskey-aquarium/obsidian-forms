<?php

namespace Obsidian_Forms;

use Obsidian_Forms\Models\Form;
use WP_Error;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Model for working with form entries.
 *
 * @since   0.1.0
 * @version 0.1.0
 */
class Entry {

	/**
	 * Entry ID.
	 *
	 * @var int
	 */
	protected int $id = 0;

	/**
	 * Entry data.
	 *
	 * @var array
	 */
	protected array $data = [];

	/**
	 * Entry meta data.
	 *
	 * @var array
	 */
	protected array $meta = [];

	/**
	 * Constructor.
	 *
	 * @param int $id Entry ID (optional).
	 */
	public function __construct( int $id = 0 ) {
		if ( $id > 0 ) {
			$this->id = $id;
			$this->load_entry( $id );
		}
	}

	/**
	 * Create a new entry.
	 *
	 * @param int   $form_id The form ID.
	 * @param array $data    The submitted data.
	 *
	 * @return int|WP_Error Entry ID on success, WP_Error on failure.
	 */
	public function create( int $form_id, array $data ) {
		global $wpdb;

		$entries_table = Database::get_entries_table();

		// Get IP address and user agent.
		$ip_address = $this->get_ip_address();
		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '';

		// Get current user ID if logged in.
		$user_id = get_current_user_id();

		// Insert entry.
		$result = $wpdb->insert(
			$entries_table,
			[
				'form_id'    => $form_id,
				'status'     => 'unread',
				'ip_address' => $ip_address,
				'user_agent' => $user_agent,
				'user_id'    => $user_id > 0 ? $user_id : null,
			],
			[ '%d', '%s', '%s', '%s', '%d' ]
		);

		if ( false === $result ) {
			return new WP_Error( 'db_error', __( 'Failed to create entry.', 'obsidian-forms' ) );
		}

		$entry_id = $wpdb->insert_id;

		// Save entry meta.
		$this->save_entry_meta( $entry_id, $form_id, $data );

		$this->id = $entry_id;

		return $entry_id;
	}

	/**
	 * Save entry meta data.
	 *
	 * @param int   $entry_id The entry ID.
	 * @param int   $form_id  The form ID.
	 * @param array $data     The submitted data.
	 *
	 * @return void
	 */
	protected function save_entry_meta( int $entry_id, int $form_id, array $data ): void {
		global $wpdb;

		$entry_meta_table = Database::get_entry_meta_table();

		// Get form fields to map labels and types.
		$form = new Form( $form_id );
		$fields = $form->get_fields();

		// Create a map of field names to field objects.
		$field_map = [];

		foreach ( $fields as $field ) {
			$field_name = $field->get_attribute( 'fieldName', '' );

			if ( ! empty( $field_name ) ) {
				$field_map[ $field_name ] = $field;
			}
		}

		// Save each field value.
		foreach ( $data as $field_name => $field_value ) {
			// Skip non-field data.
			if ( in_array( $field_name, [ 'obsidian_form_id', 'obsidian_form_nonce', 'obsidian_form_rest_nonce', 'action' ], true ) ) {
				continue;
			}

			// Get field info.
			$field = $field_map[ $field_name ] ?? null;
			$field_label = $field ? $field->get_attribute( 'fieldLabel', $field_name ) : $field_name;
			$field_type = $field ? $field->get_attribute( 'fieldType', 'text' ) : 'text';

			// Sanitize value.
			if ( $field ) {
				$field_value = $field->sanitize( $field_value );
			} else {
				$field_value = sanitize_text_field( $field_value );
			}

			// Serialize arrays.
			if ( is_array( $field_value ) ) {
				$field_value = maybe_serialize( $field_value );
			}

			// Insert meta.
			$wpdb->insert(
				$entry_meta_table,
				[
					'entry_id'    => $entry_id,
					'field_name'  => $field_name,
					'field_label' => $field_label,
					'field_type'  => $field_type,
					'field_value' => $field_value,
				],
				[ '%d', '%s', '%s', '%s', '%s' ]
			);
		}
	}

	/**
	 * Load entry data.
	 *
	 * @param int $entry_id The entry ID.
	 *
	 * @return void
	 */
	protected function load_entry( int $entry_id ): void {
		global $wpdb;

		$entries_table = Database::get_entries_table();
		$entry_meta_table = Database::get_entry_meta_table();

		// Get entry data.
		$entry = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$entries_table} WHERE id = %d", $entry_id ),
			ARRAY_A
		);

		if ( $entry ) {
			$this->data = $entry;
		}

		// Get entry meta.
		$meta = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$entry_meta_table} WHERE entry_id = %d", $entry_id ),
			ARRAY_A
		);

		if ( $meta ) {
			foreach ( $meta as $meta_row ) {
				$this->meta[ $meta_row['field_name'] ] = [
					'label' => $meta_row['field_label'],
					'type'  => $meta_row['field_type'],
					'value' => maybe_unserialize( $meta_row['field_value'] ),
				];
			}
		}
	}

	/**
	 * Get entry by ID.
	 *
	 * @param int $entry_id The entry ID.
	 *
	 * @return array|null Entry data with meta, or null if not found.
	 */
	public function get_entry( int $entry_id ): ?array {
		$entry = new self( $entry_id );

		if ( empty( $entry->data ) ) {
			return null;
		}

		return [
			'entry' => $entry->data,
			'meta'  => $entry->meta,
		];
	}

	/**
	 * Update entry status.
	 *
	 * @param int    $entry_id The entry ID.
	 * @param string $status   The new status.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function update_status( int $entry_id, string $status ): bool {
		global $wpdb;

		$entries_table = Database::get_entries_table();

		$result = $wpdb->update(
			$entries_table,
			[ 'status' => $status ],
			[ 'id' => $entry_id ],
			[ '%s' ],
			[ '%d' ]
		);

		return false !== $result;
	}

	/**
	 * Delete an entry and its meta.
	 *
	 * @param int $entry_id The entry ID.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function delete( int $entry_id ): bool {
		global $wpdb;

		$entries_table = Database::get_entries_table();
		$entry_meta_table = Database::get_entry_meta_table();

		// Delete entry meta.
		$wpdb->delete(
			$entry_meta_table,
			[ 'entry_id' => $entry_id ],
			[ '%d' ]
		);

		// Delete entry.
		$result = $wpdb->delete(
			$entries_table,
			[ 'id' => $entry_id ],
			[ '%d' ]
		);

		return false !== $result;
	}

	/**
	 * Get entries for a form.
	 *
	 * @param int   $form_id The form ID.
	 * @param array $args    Query arguments.
	 *
	 * @return array Array of entries.
	 */
	public function get_entries( int $form_id, array $args = [] ): array {
		global $wpdb;

		$entries_table = Database::get_entries_table();

		// Parse arguments.
		$defaults = [
			'status'  => '',
			'limit'   => 20,
			'offset'  => 0,
			'orderby' => 'created_at',
			'order'   => 'DESC',
		];

		$args = wp_parse_args( $args, $defaults );

		// Build query.
		$where = $wpdb->prepare( 'WHERE form_id = %d', $form_id );

		if ( ! empty( $args['status'] ) ) {
			$where .= $wpdb->prepare( ' AND status = %s', $args['status'] );
		}

		$orderby = sanitize_sql_orderby( $args['orderby'] . ' ' . $args['order'] );
		$limit = absint( $args['limit'] );
		$offset = absint( $args['offset'] );

		$query = "SELECT * FROM {$entries_table} {$where} ORDER BY {$orderby} LIMIT {$limit} OFFSET {$offset}";

		$entries = $wpdb->get_results( $query, ARRAY_A );

		return $entries ? $entries : [];
	}

	/**
	 * Get entry data.
	 *
	 * @return array
	 */
	public function get_data(): array {
		return $this->data;
	}

	/**
	 * Get entry meta.
	 *
	 * @return array
	 */
	public function get_meta(): array {
		return $this->meta;
	}

	/**
	 * Get the user's IP address.
	 *
	 * @return string
	 */
	protected function get_ip_address(): string {
		$ip = '';

		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return sanitize_text_field( $ip );
	}
}

