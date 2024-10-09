import { __ } from '@wordpress/i18n';
import { RichText } from '@wordpress/block-editor';

/**
 * Component for rendering form settings.
 *
 * @param {Object}   props                   Props passed to the component.
 * @param {Object}   props.attributes        Attributes passed to the component.
 * @param {string}   props.requiredIndicator The required indicator.
 * @param {Function} props.handleLabelChange Function to handle label change.
 *
 * @return {Object} The rendered component.
 */
const ObsidianFieldLabel = ( {
	attributes,
	requiredIndicator,
	handleLabelChange,
} ) => {
	const { fieldLabel, fieldName, isRequired } = attributes;

	return (
		<>
			<label
				className="wp-block-obsidian-form-field__label"
				htmlFor={ fieldName }
			>
				<RichText
					value={ fieldLabel }
					onChange={ handleLabelChange }
					placeholder={ __( 'Enter Field Label', 'obsidian-forms' ) }
					tag="label"
					className="wp-block-obsidian-form-field__label"
				/>
				{ isRequired && <span>{ requiredIndicator }</span> }
			</label>
		</>
	);
};

export default ObsidianFieldLabel;
