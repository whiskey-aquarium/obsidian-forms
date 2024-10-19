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
			$args = [];

			// Conditionally set the parent for the "Field" block.
			if ( strpos( $block, 'obsidian-form/field-group' ) !== false && ! $this->is_obsidian_form_post_type() ) {
				$args['parent'] = [ 'obsidian-form/form' ];
			}

			// Register the block with the modified args.
			register_block_type_from_metadata( $block, $args );
		}
	}

	/**
	 * Checks if the current post type is obsidian_form.
	 *
	 * @return bool
	 */
	private function is_obsidian_form_post_type(): bool {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		return $screen && 'obsidian_form' === $screen->post_type;
	}

}
