<?php

namespace Obsidian_Forms;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles database table creation and management.
 *
 * @since   0.1.0
 * @version 0.1.0
 */
class Database {

	/**
	 * Database version.
	 *
	 * @var string
	 */
	const DB_VERSION = '1.0.0';

	/**
	 * Initialize database setup.
	 *
	 * @return void
	 */
	public function initialize(): void {
		// Check if tables need to be created on admin init.
		add_action( 'admin_init', [ $this, 'maybe_create_tables' ] );

		// Hook into plugin activation.
		register_activation_hook( OBSIDIAN_FORMS_FILE, [ $this, 'create_tables' ] );
	}

	/**
	 * Maybe create tables if they don't exist or version has changed.
	 *
	 * @return void
	 */
	public function maybe_create_tables(): void {
		$installed_version = get_option( 'obsidian_forms_db_version', '0.0.0' );

		if ( version_compare( $installed_version, self::DB_VERSION, '<' ) ) {
			$this->create_tables();
			update_option( 'obsidian_forms_db_version', self::DB_VERSION );
		}
	}

	/**
	 * Create database tables.
	 *
	 * @return void
	 */
	public function create_tables(): void {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		// Entries table.
		$entries_table = $wpdb->prefix . 'obsidian_form_entries';

		$entries_sql = "CREATE TABLE IF NOT EXISTS {$entries_table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			form_id bigint(20) unsigned NOT NULL,
			status varchar(20) NOT NULL DEFAULT 'unread',
			ip_address varchar(100) DEFAULT '',
			user_agent text DEFAULT '',
			user_id bigint(20) unsigned DEFAULT NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY form_id (form_id),
			KEY status (status),
			KEY created_at (created_at),
			KEY user_id (user_id)
		) {$charset_collate};";

		// Entry meta table.
		$entry_meta_table = $wpdb->prefix . 'obsidian_form_entry_meta';

		$entry_meta_sql = "CREATE TABLE IF NOT EXISTS {$entry_meta_table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			entry_id bigint(20) unsigned NOT NULL,
			field_name varchar(255) NOT NULL,
			field_label varchar(255) DEFAULT '',
			field_type varchar(50) DEFAULT 'text',
			field_value longtext,
			PRIMARY KEY (id),
			KEY entry_id (entry_id),
			KEY field_name (field_name)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $entries_sql );
		dbDelta( $entry_meta_sql );
	}

	/**
	 * Get entries table name.
	 *
	 * @return string
	 */
	public static function get_entries_table(): string {
		global $wpdb;

		return $wpdb->prefix . 'obsidian_form_entries';
	}

	/**
	 * Get entry meta table name.
	 *
	 * @return string
	 */
	public static function get_entry_meta_table(): string {
		global $wpdb;

		return $wpdb->prefix . 'obsidian_form_entry_meta';
	}
}

