<?php

namespace Obsidian_Forms;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The main Obsidian Forms plugin class.
 *
 * @since   0.1.0
 * @version 0.1.0
 */
class Obsidian_Forms {
	/**
	 * The blocks component.
	 *
	 * @since   0.1.0
	 * @version 0.1.0
	 *
	 * @var     Blocks|null
	 */
	public ?Blocks $blocks = null;

	/**
	 * Plugin constructor.
	 *
	 * @since   0.1.0
	 * @version 0.1.0
	 */
	protected function __construct() {
		/* Left empty on purpose. */
	}

	/**
	 * Prevent cloning.
	 *
	 * @since   0.1.0
	 * @version 0.1.0
	 *
	 * @return  void
	 */
	private function __clone() {
		/* Left empty on purpose. */
	}

	/**
	 * Prevent deserialization.
	 *
	 * @since   0.1.0
	 * @version 0.1.0
	 *
	 * @return  void
	 */
	public function __wakeup() {
		/* Left empty on purpose. */
	}

	/**
	 * Returns a singleton instance of the plugin.
	 *
	 * This method should be used to instantiate the plugin class.
	 *
	 * @since   0.1.0
	 * @version 0.1.0
	 *
	 * @return Obsidian_Forms
	 */
	public static function get_instance(): self {
		static $instance = null;

		if ( null === $instance ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Initializes the plugin components.
	 *
	 * @since   0.1.0
	 * @version 0.1.0
	 *
	 * @return  void
	 */
	public function initialize(): void {
		( new Blocks() )->initialize();
		( new Admin() )->initialize();
	}
}
