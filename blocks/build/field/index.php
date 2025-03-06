<?php
/**
 * Obsidian Field block markup
 *
 * @var array    $attributes         Block attributes.
 * @var string   $content            Block content.
 * @var WP_Block $block              Block instance.
 * @var array    $context            Block context.
 */

$obsidian_forms_field_class = [
	'obsidian-forms-field__' . $attributes['fieldType'],
];

if ( $attributes['isRequired'] ) {
	$obsidian_forms_field_class[] = 'obsidian-forms-field__required';
}

if ( $attributes['formId'] ) {
	$obsidian_forms_field_class[] = 'obsidian-forms-field__' . $attributes['formId'];

}

if ( $attributes['fieldName'] ) {
	$obsidian_forms_field_class[] = 'obsidian-forms-field__name-' . $attributes['fieldName'];
}

$obsidian_forms_field_block_wrapper_attributes = [
	'class' => implode( ' ', $obsidian_forms_field_class ),
];

if ( $attributes['fieldWidth'] ) {
	$obsidian_forms_field_block_wrapper_attributes['style'] = '--of-field-flex: 0 0 ' . $attributes['fieldWidth'];
}

$obsidian_forms_field_args = apply_filters(
	'obsidian_forms_field_args_' . $attributes['formId'],
	[
		'form_id'           => $attributes['formId'] ?? '',
		'field_label'       => $attributes['fieldLabel'] ?? '',
		'field_name'        => $attributes['fieldName'] ?? '',
		'field_type'        => $attributes['fieldType'] ?? 'text',
		'field_placeholder' => $attributes['fieldPlaceholder'] ?? '',
		'field_description' => $attributes['fieldDescription'] ?? '',
		'field_width'       => $attributes['fieldWidth'] ?? '',
		'field_required'    => $attributes['isRequired'] ?? false,
		'field_options'     => $attributes['fieldOptions'] ?? [],
		'field_extra_props' => $attributes['extraProps'] ?? [],
		'field_class'       => $obsidian_forms_field_class,
		'block_attributes'  => get_block_wrapper_attributes( $obsidian_forms_field_block_wrapper_attributes ),
	],
);

$obsidian_forms_form_settings = $block->context['obsidian-form/formSettings'] ?? [];
$description_placement = $obsidian_forms_form_settings['descriptionPlacement'] ?? 'bottom';
?>

<div <?php echo wp_kses_data( $obsidian_forms_field_args['block_attributes'] ); ?>>
	<label
		for="<?php echo esc_attr( $obsidian_forms_field_args['field_name'] ); ?>"
		class="wp-block-obsidian-form-field__label"
	>
		<?php echo esc_html( $obsidian_forms_field_args['field_label'] ); ?>

		<?php if ( $obsidian_forms_field_args['field_required'] ) : ?>
			<span>*</span>
		<?php endif; ?>
	</label>

	<?php if ( $description_placement === 'top' ) : ?>
		<div class="wp-block-obsidian-form-field__description">
			<small>
				<?php echo esc_html( $obsidian_forms_field_args['field_description'] ); ?>
			</small>
		</div>
	<?php endif; ?>

	<?php if ( 'textarea' === $obsidian_forms_field_args['field_type'] ) : ?>
		<textarea
			id="<?php echo esc_attr( $obsidian_forms_field_args['field_name'] ); ?>"
			name="<?php echo esc_attr( $obsidian_forms_field_args['field_name'] ); ?>"
			placeholder="<?php echo esc_attr( $obsidian_forms_field_args['field_placeholder'] ); ?>"
			row="5"
		></textarea>
	<?php elseif ( 'select' === $obsidian_forms_field_args['field_type'] ) : ?>
		<select
			id="<?php echo esc_attr( $obsidian_forms_field_args['field_name'] ); ?>"
			name="<?php echo esc_attr( $obsidian_forms_field_args['field_name'] ); ?>"
		>
			<?php foreach ( $obsidian_forms_field_args['field_options'] as $obsidian_forms_field_option ) : ?>
				<option value="<?php echo esc_attr( $obsidian_forms_field_option['value'] ); ?>">
					<?php echo esc_html( $obsidian_forms_field_option['label'] ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	<?php elseif ( 'checkbox' === $obsidian_forms_field_args['field_type'] || 'radio' === $obsidian_forms_field_args['field_type'] ) : ?>
		<?php
			$obsidian_forms_field_type        = $obsidian_forms_field_args['field_type'];
			$obsidian_forms_field_type_plural = 'checkbox' === $obsidian_forms_field_type ? 'checkboxes' : 'radios';
			$obsidian_forms_extra_props       = $obsidian_forms_field_args['field_extra_props'];
			$obsidian_forms_options_layout    = 'checkbox' === $obsidian_forms_field_type ? ( $obsidian_forms_extra_props['checkboxesLayout'] ?? 'stacked' ) : ( $obsidian_forms_extra_props['radioLayout'] ?? 'stacked' );
		?>

		<div class="<?php echo esc_attr( "wp-block-obsidian-form-field__$obsidian_forms_field_type_plural wp-block-obsidian-form-field__$obsidian_forms_field_type_plural--$obsidian_forms_options_layout" ); ?>">
			<?php foreach ( $obsidian_forms_field_args['field_options'] as $obsidian_forms_field_option ) : ?>
				<div class="<?php echo esc_attr( "wp-block-obsidian-form-field__$obsidian_forms_field_type" ); ?>">
					<input
						type="<?php echo esc_attr( $obsidian_forms_field_args['field_type'] ); ?>"
						id="<?php echo esc_attr( $obsidian_forms_field_args['field_name'] . '-' . $obsidian_forms_field_option['value'] ); ?>"
						name="<?php echo esc_attr( $obsidian_forms_field_args['field_name'] ); ?>"
						value="<?php echo esc_attr( $obsidian_forms_field_option['value'] ); ?>"
					>
					<label for="<?php echo esc_attr( $obsidian_forms_field_args['field_name'] . '-' . $obsidian_forms_field_option['value'] ); ?>">
						<?php echo esc_html( $obsidian_forms_field_option['label'] ); ?>
					</label>
				</div>
			<?php endforeach; ?>
		</div>
	<?php else : ?>
		<input
			type="<?php echo esc_attr( $obsidian_forms_field_args['field_type'] ); ?>"
			id="<?php echo esc_attr( $obsidian_forms_field_args['field_name'] ); ?>"
			name="<?php echo esc_attr( $obsidian_forms_field_args['field_name'] ); ?>"
			placeholder="<?php echo esc_attr( $obsidian_forms_field_args['field_placeholder'] ); ?>"
			class="wp-block-obsidian-form-field__input"
		>
	<?php endif; ?>

	<?php if ( $description_placement === 'bottom' ) : ?>
		<div class="wp-block-obsidian-form-field__description">
			<small>
				<?php echo esc_html( $obsidian_forms_field_args['field_description'] ); ?>
			</small>
		</div>
	<?php endif; ?>
</div>
