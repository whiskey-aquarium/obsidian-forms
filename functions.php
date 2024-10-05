<?php
/**
 * Obsidian Forms Functions
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Obsidian_Forms\Obsidian_Forms;

if ( ! function_exists( 'obsidian_forms_get_plugin_instance' ) ) {
	/**
	 * Get the Obsidian Forms plugin instance.
	 *
	 * @return Obsidian_Forms
	 */
	function obsidian_forms_get_plugin_instance(): Obsidian_Forms {
		return Obsidian_Forms::get_instance();
	}
}