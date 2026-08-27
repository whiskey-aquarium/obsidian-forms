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
use Obsidian_Forms\Models\Form;

$obsidian_forms_form_post_id  = absint( $attributes['formPostId'] ?? 0 );
$obsidian_forms_form_post     = $obsidian_forms_form_post_id ? get_post( $obsidian_forms_form_post_id ) : null;
$obsidian_forms_form_settings = [];
$obsidian_forms_content       = $content;
$obsidian_forms_id_prefix     = wp_unique_id( 'obsidian-form-' . $obsidian_forms_form_post_id . '-' );

if (
	$obsidian_forms_form_post instanceof WP_Post &&
	'obsidian_form' === $obsidian_forms_form_post->post_type &&
	( 'publish' === $obsidian_forms_form_post->post_status || current_user_can( 'manage_options' ) )
) {
	$obsidian_forms_model          = new Form( $obsidian_forms_form_post_id );
	$obsidian_forms_form_settings  = $obsidian_forms_model->get_settings();
	$obsidian_forms_saved_settings = get_post_meta( $obsidian_forms_form_post_id, '_obsidian_form_settings', true );

	if ( is_array( $obsidian_forms_saved_settings ) ) {
		$obsidian_forms_form_settings = array_merge( $obsidian_forms_form_settings, $obsidian_forms_saved_settings );
	}

	$obsidian_forms_content = '';

	foreach ( parse_blocks( $obsidian_forms_form_post->post_content ) as $obsidian_forms_parsed_block ) {
		$obsidian_forms_reusable_block = new WP_Block(
			$obsidian_forms_parsed_block,
			[
				'obsidian-form/formSettings' => $obsidian_forms_form_settings,
				'obsidian-form/idPrefix'     => $obsidian_forms_id_prefix,
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
		'block_attributes' => get_block_wrapper_attributes(),
		'form_action'      => admin_url( 'admin-post.php' ),
	],
);

if ( ! $obsidian_forms_content ) {
	return;
}
?>

<form
	<?php echo wp_kses_data( $obsidian_forms_form_args['block_attributes'] ); ?>
	method="post"
	action="<?php echo esc_url( $obsidian_forms_form_args['form_action'] ); ?>"
>
	<input type="hidden" name="action" value="obsidian_forms_submit">
	<input type="hidden" name="obsidian_form_id" value="<?php echo esc_attr( $obsidian_forms_form_post_id ); ?>">
	<input type="hidden" name="obsidian_form_redirect" value="<?php echo esc_url( remove_query_arg( 'obsidian_form_status' ) ); ?>">
	<?php wp_nonce_field( 'obsidian_form_submit_' . $obsidian_forms_form_post_id, 'obsidian_form_nonce' ); ?>
	<div class="obsidian-forms-honeypot" aria-hidden="true">
		<label for="<?php echo esc_attr( $obsidian_forms_id_prefix . 'website' ); ?>"><?php esc_html_e( 'Website', 'obsidian-forms' ); ?></label>
		<input id="<?php echo esc_attr( $obsidian_forms_id_prefix . 'website' ); ?>" type="text" name="obsidian_form_website" tabindex="-1" autocomplete="off">
	</div>

	<?php if ( $obsidian_forms_form_args['form_post'] instanceof WP_Post ) : ?>
		<h2 class="obsidian-form-title"><?php echo esc_html( get_the_title( $obsidian_forms_form_args['form_post'] ) ); ?></h2>
	<?php endif; ?>

	<?php
	$obsidian_forms_status = sanitize_key( wp_unslash( $_GET['obsidian_form_status'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only result display.
	if ( 'success' === $obsidian_forms_status ) :
		?>
		<div class="obsidian-forms-notice obsidian-forms-notice--success" role="status"><?php echo esc_html( $obsidian_forms_form_settings['successMessage'] ?? __( 'Thanks! Your form was submitted.', 'obsidian-forms' ) ); ?></div>
	<?php elseif ( $obsidian_forms_status ) : ?>
		<div class="obsidian-forms-notice obsidian-forms-notice--error" role="alert"><?php echo esc_html( $obsidian_forms_form_settings['errorMessage'] ?? __( 'The form could not be submitted. Check your entries and try again.', 'obsidian-forms' ) ); ?></div>
	<?php endif; ?>

	<?php echo $obsidian_forms_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Rendered block markup. ?>

	<div class="wp-block-obsidian-form-field-group">
		<div class="wp-block-obsidian-form-field">
			<button type="submit" class="obsidian-form-submit"><?php echo esc_html( $obsidian_forms_form_settings['submitButtonText'] ?? __( 'Submit', 'obsidian-forms' ) ); ?></button>
		</div>
	</div>
</form>
