<?php
/**
 * Obsidian Field Group block markup
 *
 * @var array    $attributes         Block attributes.
 * @var string   $content            Block content.
 * @var WP_Block $block              Block instance.
 * @var array    $context            Block context.
 */

$obsidian_forms_field_group_block_wrapper_attributes = [];

if ( count( $block->parsed_block['innerBlocks'] ) > 1 ) {
	$obsidian_forms_field_group_block_wrapper_attributes['style'] = '--of-field-flex: ' . 100/count( $block->parsed_block['innerBlocks'] ) . '%';
}

$obsidian_forms_field_group_args = apply_filters(
	'obsidian_forms_field_group_args',
	[
		'block_attributes' => get_block_wrapper_attributes( $obsidian_forms_field_group_block_wrapper_attributes ),
	],
);

if ( ! $content ) {
	return;
}
?>

<div <?php echo wp_kses_data( $obsidian_forms_field_group_args['block_attributes'] ); ?>>
	<?php echo $content; ?>
</div>
