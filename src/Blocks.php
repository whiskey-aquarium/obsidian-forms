<?php

namespace Obsidian_Forms;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the registration of blocks.
 *
 * @since   0.1.0
 * @version 0.1.0
 */
final class Blocks {
	/**
	 * Initializes the blocks.
	 *
	 * @since   0.1.0
	 * @version 0.1.0
	 *
	 * @return  void
	 */
	public function initialize(): void {
		add_action( 'init', [ $this, 'register_blocks' ] );
	}

	/**
	 * Registers the blocks.
	 *
	 * @since   0.1.0
	 * @version 0.1.0
	 *
	 * @return  void
	 */
	public function register_blocks(): void {
		$blocks = glob( OBSIDIAN_FORMS_PATH . 'blocks/build/*', GLOB_ONLYDIR );

		foreach ( $blocks as $block ) {
			register_block_type_from_metadata( $block );
		}
	}
}
