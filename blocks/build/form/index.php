<?php
/**
 * Obsidian Form block markup
 *
 * @var array    $attributes         Block attributes.
 * @var string   $content            Block content.
 * @var WP_Block $block              Block instance.
 * @var array    $context            Block context.
 */

use Obsidian_Forms\Form_Renderer;

// Get form ID.
$form_id = absint( $attributes['formPostId'] ?? 0 );

if ( empty( $form_id ) ) {
	return;
}

// Use Form_Renderer to render the form.
$renderer = new Form_Renderer();
echo $renderer->render_form( $form_id );

