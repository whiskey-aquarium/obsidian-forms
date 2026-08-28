<?php
/**
 * Obsidian Field block markup
 *
 * @var array    $attributes         Block attributes.
 * @var string   $content            Block content.
 * @var WP_Block $block              Block instance.
 * @var array    $context            Block context.
 */

$obsidian_forms_allowed_types  = [ 'text', 'url', 'email', 'number', 'date', 'time', 'textarea', 'select', 'checkbox', 'radio', 'tel', 'range' ];
$obsidian_forms_field_type     = sanitize_key( $attributes['fieldType'] ?? 'text' );
$obsidian_forms_field_type     = in_array( $obsidian_forms_field_type, $obsidian_forms_allowed_types, true ) ? $obsidian_forms_field_type : 'text';
$obsidian_forms_field_name     = sanitize_key( $attributes['fieldName'] ?? '' );
$obsidian_forms_id_prefix      = $block->context['obsidian-form/idPrefix'] ?? wp_unique_id( 'obsidian-form-field-' );
$obsidian_forms_field_id       = $obsidian_forms_id_prefix . ( $obsidian_forms_field_name ? $obsidian_forms_field_name : wp_unique_id( 'field-' ) );
$obsidian_forms_description_id = $obsidian_forms_field_id . '-description';
$obsidian_forms_field_class    = [
	'obsidian-forms-field__' . $obsidian_forms_field_type,
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
		'field_name'        => $obsidian_forms_field_name,
		'field_type'        => $obsidian_forms_field_type,
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

$obsidian_forms_form_settings         = $block->context['obsidian-form/formSettings'] ?? [];
$obsidian_forms_description_placement = $obsidian_forms_form_settings['descriptionPlacement'] ?? 'bottom';
$obsidian_forms_required_indicator    = $obsidian_forms_form_settings['requiredIndicator'] ?? '*';
$obsidian_forms_has_placeholder       = $obsidian_forms_form_settings['globalHasPlaceholder'] ?? true;
?>

<div <?php echo wp_kses_data( $obsidian_forms_field_args['block_attributes'] ); ?>>
	<label
		for="<?php echo esc_attr( $obsidian_forms_field_id ); ?>"
		class="wp-block-obsidian-form-field__label"
	>
		<?php echo esc_html( $obsidian_forms_field_args['field_label'] ); ?>

		<?php if ( $obsidian_forms_field_args['field_required'] ) : ?>
			<span><?php echo esc_html( $obsidian_forms_required_indicator ); ?></span>
		<?php endif; ?>
	</label>

	<?php if ( $obsidian_forms_field_args['field_description'] && 'top' === $obsidian_forms_description_placement ) : ?>
		<div id="<?php echo esc_attr( $obsidian_forms_description_id ); ?>" class="wp-block-obsidian-form-field__description">
			<small>
				<?php echo esc_html( $obsidian_forms_field_args['field_description'] ); ?>
			</small>
		</div>
	<?php endif; ?>

	<?php if ( 'textarea' === $obsidian_forms_field_args['field_type'] ) : ?>
		<textarea
			id="<?php echo esc_attr( $obsidian_forms_field_id ); ?>"
			name="<?php echo esc_attr( $obsidian_forms_field_args['field_name'] ); ?>"
			placeholder="<?php echo esc_attr( $obsidian_forms_has_placeholder ? $obsidian_forms_field_args['field_placeholder'] : '' ); ?>"
			rows="5"
			<?php echo $obsidian_forms_field_args['field_required'] ? 'required' : ''; ?>
			<?php echo $obsidian_forms_field_args['field_description'] ? 'aria-describedby="' . esc_attr( $obsidian_forms_description_id ) . '"' : ''; ?>
		></textarea>
	<?php elseif ( 'select' === $obsidian_forms_field_args['field_type'] ) : ?>
		<select
			id="<?php echo esc_attr( $obsidian_forms_field_id ); ?>"
			name="<?php echo esc_attr( $obsidian_forms_field_args['field_name'] ); ?>"
			<?php echo $obsidian_forms_field_args['field_required'] ? 'required' : ''; ?>
			<?php echo $obsidian_forms_field_args['field_description'] ? 'aria-describedby="' . esc_attr( $obsidian_forms_description_id ) . '"' : ''; ?>
		>
			<?php foreach ( $obsidian_forms_field_args['field_options'] as $obsidian_forms_field_option ) : ?>
				<?php $obsidian_forms_option_value = ! empty( $obsidian_forms_field_option['value'] ) ? $obsidian_forms_field_option['value'] : sanitize_title( $obsidian_forms_field_option['label'] ?? '' ); ?>
				<option value="<?php echo esc_attr( $obsidian_forms_option_value ); ?>">
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
				<?php $obsidian_forms_option_value = ! empty( $obsidian_forms_field_option['value'] ) ? $obsidian_forms_field_option['value'] : sanitize_title( $obsidian_forms_field_option['label'] ?? '' ); ?>
				<div class="<?php echo esc_attr( "wp-block-obsidian-form-field__$obsidian_forms_field_type" ); ?>">
					<input
						type="<?php echo esc_attr( $obsidian_forms_field_args['field_type'] ); ?>"
						id="<?php echo esc_attr( $obsidian_forms_field_id . '-' . sanitize_key( $obsidian_forms_option_value ) ); ?>"
						name="<?php echo esc_attr( $obsidian_forms_field_args['field_name'] . ( 'checkbox' === $obsidian_forms_field_type ? '[]' : '' ) ); ?>"
						value="<?php echo esc_attr( $obsidian_forms_option_value ); ?>"
						<?php echo $obsidian_forms_field_args['field_required'] ? 'required' : ''; ?>
					>
					<label for="<?php echo esc_attr( $obsidian_forms_field_id . '-' . sanitize_key( $obsidian_forms_option_value ) ); ?>">
						<?php echo esc_html( $obsidian_forms_field_option['label'] ); ?>
					</label>
				</div>
			<?php endforeach; ?>
		</div>
	<?php else : ?>
		<input
			type="<?php echo esc_attr( $obsidian_forms_field_args['field_type'] ); ?>"
			id="<?php echo esc_attr( $obsidian_forms_field_id ); ?>"
			name="<?php echo esc_attr( $obsidian_forms_field_args['field_name'] ); ?>"
			placeholder="<?php echo esc_attr( $obsidian_forms_has_placeholder ? $obsidian_forms_field_args['field_placeholder'] : '' ); ?>"
			class="wp-block-obsidian-form-field__input"
			<?php echo $obsidian_forms_field_args['field_required'] ? 'required' : ''; ?>
			<?php echo $obsidian_forms_field_args['field_description'] ? 'aria-describedby="' . esc_attr( $obsidian_forms_description_id ) . '"' : ''; ?>
		>
	<?php endif; ?>

	<?php if ( $obsidian_forms_field_args['field_description'] && 'bottom' === $obsidian_forms_description_placement ) : ?>
		<div id="<?php echo esc_attr( $obsidian_forms_description_id ); ?>" class="wp-block-obsidian-form-field__description">
			<small>
				<?php echo esc_html( $obsidian_forms_field_args['field_description'] ); ?>
			</small>
		</div>
	<?php endif; ?>
</div>
