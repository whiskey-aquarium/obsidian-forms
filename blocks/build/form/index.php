<?php
/**
 * Obsidian Form block markup
 *
 * @var array    $attributes         Block attributes.
 * @var string   $content            Block content.
 * @var WP_Block $block              Block instance.
 * @var array    $context            Block context.
 */

// Resolve the reusable form and render its blocks with the form settings context.
$obsidian_forms_form_post_id  = absint( $attributes['formPostId'] ?? 0 );
$obsidian_forms_form_post     = $obsidian_forms_form_post_id ? get_post( $obsidian_forms_form_post_id ) : null;
$obsidian_forms_form_settings = [];
$obsidian_forms_content       = $content;

if ( $obsidian_forms_form_post instanceof WP_Post && 'obsidian_form' === $obsidian_forms_form_post->post_type ) {
	$obsidian_forms_saved_settings = get_post_meta( $obsidian_forms_form_post_id, '_obsidian_form_settings', true );

	if ( is_array( $obsidian_forms_saved_settings ) ) {
		$obsidian_forms_form_settings = $obsidian_forms_saved_settings;
	}

	$obsidian_forms_content = '';

	foreach ( parse_blocks( $obsidian_forms_form_post->post_content ) as $obsidian_forms_parsed_block ) {
		$obsidian_forms_reusable_block = new WP_Block(
			$obsidian_forms_parsed_block,
			[
				'obsidian-form/formSettings' => $obsidian_forms_form_settings,
			]
		);
		$obsidian_forms_content       .= $obsidian_forms_reusable_block->render();
	}
}

$obsidian_forms_form_args = apply_filters(
	'obsidian_forms_form_args',
	[
		'form_settings'    => $obsidian_forms_form_settings,
		'form_post'        => $obsidian_forms_form_post,
		'block_attributes' => get_block_wrapper_attributes(
			[
				'novalidate' => true,
				'method'     => 'post',
				'action'     => esc_url( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) ), // Set action to the current URL by default.
			]
		),
	],
);

if ( ! $obsidian_forms_content ) {
	return;
}
?>

<form <?php echo wp_kses_data( $obsidian_forms_form_args['block_attributes'] ); ?>>
	<?php if ( $obsidian_forms_form_args['form_post'] instanceof WP_Post ) : ?>
		<h2 class="obsidian-form-title"><?php echo esc_html( get_the_title( $obsidian_forms_form_args['form_post'] ) ); ?></h2>
	<?php endif; ?>

	<?php echo $obsidian_forms_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Rendered block markup. ?>

	<div class="wp-block-obsidian-form-field-group">
		<div class="wp-block-obsidian-form-field">
			<button type="submit" id="obsidian-form-submit">Submit</button> <?php // @todo: Add button text option or submit button block. ?>
		</div>
	</div>
</form>
