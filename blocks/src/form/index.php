<?php
/**
 * Obsidian Form block markup
 *
 * @var array    $attributes         Block attributes.
 * @var string   $content            Block content.
 * @var WP_Block $block              Block instance.
 * @var array    $context            Block context.
 */

$obsidian_forms_form_args = apply_filters(
	'obsidian_forms_form_args',
	[
		'form_settings'    => $attributes['formSettings'] ?? [],
		'block_attributes' => get_block_wrapper_attributes(),
	],
);

if ( ! $content ) {
	return;
}
?>

<form <?php echo wp_kses_data( $obsidian_forms_form_args['block_attributes'] ); ?>>
	<?php if ( ! empty( $obsidian_forms_form_args['form_settings']['title'] ) ) : ?>
		<h2 class="obsidian-form-title"><?php echo esc_html( $obsidian_forms_form_args['form_settings']['title']['value'] ); ?></h2>
	<?php endif; ?>

	<?php echo $content; ?>
</form>
