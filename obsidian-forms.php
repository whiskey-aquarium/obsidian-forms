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
 * Tested up to:            6.5
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

define( 'OBSIDIAN_FORMS_METADATA', get_plugin_data( __FILE__, false, false ) );
define( 'OBSIDIAN_FORMS_BASENAME', plugin_basename( __FILE__ ) );
define( 'OBSIDIAN_FORMS_PATH', plugin_dir_path( __FILE__ ) );
define( 'OBSIDIAN_FORMS_URL', plugin_dir_url( __FILE__ ) );

// Autoloader
require_once OBSIDIAN_FORMS_PATH . '/vendor/autoload.php';
require_once OBSIDIAN_FORMS_PATH . 'functions.php';

add_action( 'plugins_loaded', [ obsidian_forms_get_plugin_instance(), 'initialize' ] );
