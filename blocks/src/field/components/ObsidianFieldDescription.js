import { __ } from '@wordpress/i18n';
import { RichText } from '@wordpress/block-editor';

/**
 * Component for rendering form settings.
 *
 * @param {Object}   props                   Props passed to the component.
 * @param {Object}   props.attributes        Attributes passed to the component.
 * @param {Function} props.handleDescriptionChange Function to handle description change.
 *
 * @return {Object} The rendered component.
 */
const ObsidianFieldDescription = ( {
	attributes,
	handleDescriptionChange,
} ) => {
	const { fieldDescription } = attributes;

	return (
		<>
			<small
				className="wp-block-obsidian-form-field__description"
			>
				<RichText
					value={ fieldDescription }
					onChange={ handleDescriptionChange }
					className="wp-block-obsidian-form-field__description"
				/>
			</small>
		</>
	);
};

export default ObsidianFieldDescription;
