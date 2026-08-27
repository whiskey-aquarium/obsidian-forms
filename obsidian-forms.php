<?php
/**
 * Obsidian Forms
 *
 * @since       0.1.0
 * @version     0.1.0
 * @author      Obsidian Forms
 * @license     GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:             Obsidian Forms
 * Plugin URI:              https://obsidianforms.com
 * Description:             A powerful block-based form builder for WordPress.
 * Version:                 0.1.0
 * Requires at least:       6.5
 * Tested up to:            7.1
 * Requires PHP:            7.4
 * Author:                  Obsidian Forms
 * Author URI:              https://obsidianforms.com
 * License:                 GPL v3 or later
 * License URI:             https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:             obsidian-forms
 * Domain Path:             /languages
 **/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'get_plugin_data' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

define( 'OBSIDIAN_FORMS_FILE', __FILE__ );
define( 'OBSIDIAN_FORMS_METADATA', get_plugin_data( __FILE__, false, false ) );
define( 'OBSIDIAN_FORMS_BASENAME', plugin_basename( __FILE__ ) );
define( 'OBSIDIAN_FORMS_PATH', plugin_dir_path( __FILE__ ) );
define( 'OBSIDIAN_FORMS_URL', plugin_dir_url( __FILE__ ) );

// Prefer Composer's optimized autoloader in release packages, while keeping a
// source checkout activatable for contributors and local WordPress installs.
if ( file_exists( OBSIDIAN_FORMS_PATH . 'vendor/autoload.php' ) ) {
	require_once OBSIDIAN_FORMS_PATH . 'vendor/autoload.php';
} else {
	spl_autoload_register(
		static function ( string $class_name ): void {
			$prefix = 'Obsidian_Forms\\';

			if ( 0 !== strpos( $class_name, $prefix ) ) {
				return;
			}

			$relative_class = substr( $class_name, strlen( $prefix ) );
			$relative_path  = str_replace( '\\', '/', $relative_class ) . '.php';
			$file           = OBSIDIAN_FORMS_PATH . 'src/' . $relative_path;

			if ( 0 === strpos( $relative_path, 'Models/' ) ) {
				$file = OBSIDIAN_FORMS_PATH . 'models/' . substr( $relative_path, strlen( 'Models/' ) );
			}

			if ( file_exists( $file ) ) {
				require_once $file;
			}
		}
	);
}
require_once OBSIDIAN_FORMS_PATH . 'functions.php';

add_action( 'plugins_loaded', [ obsidian_forms_get_plugin_instance(), 'initialize' ] );
