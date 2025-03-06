<?php
/**
 * Obsidian Form block markup
 *
 * @var array    $attributes         Block attributes.
 * @var string   $content            Block content.
 * @var WP_Block $block              Block instance.
 * @var array    $context            Block context.
 */

// Get form settings from post meta
$form_settings = [];
if ( ! empty( $attributes['formPostId'] ) ) {
    $meta_settings = get_post_meta( $attributes['formPostId'], '_obsidian_form_settings', true );
    if ( ! empty( $meta_settings ) ) {
        $form_settings = $meta_settings;
    }
}

$obsidian_forms_form_args = apply_filters(
	'obsidian_forms_form_args',
	[
		'form_settings'    => $form_settings,
		'block_attributes' => get_block_wrapper_attributes(
			[
				'novalidate' => true,
				'method'     => 'post',
				'action'     => esc_url( $_SERVER['REQUEST_URI'] ), // set action to current page url by default
			]
		),
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

	<div class="wp-block-obsidian-form-field-group">
		<div class="wp-block-obsidian-form-field">
			<button type="submit" id="obsidian-form-submit">Submit</button> <?php // @todo: Add button text option or submit button block. ?>
		</div>
	</div>
</form>

