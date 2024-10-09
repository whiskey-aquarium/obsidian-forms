// Import external dependencies.
import { useState } from '@wordpress/element';

// Import internal dependencies.
import ObsidianFieldLabel from '../components/ObsidianFieldLabel';

/**
 * Component for rendering form settings.
 *
 * @param {Object}   props                      Props passed to the component.
 * @param {Object}   props.attributes           Attributes passed to the component.
 * @param {boolean}  props.globalHasPlaceholder Whether the form has a placeholder.
 * @param {string}   props.requiredIndicator    The required indicator.
 * @param {Function} props.handleLabelChange    Function to handle label change.
 *
 * @return {Object} The rendered component.
 */
const ObsidianFieldInput = ( {
	attributes,
	globalHasPlaceholder,
	requiredIndicator,
	handleLabelChange,
} ) => {
	const { fieldName, fieldType, fieldPlaceholder } = attributes;

	const [ fieldValue, setFieldValue ] = useState( '' );

	return (
		<>
			<ObsidianFieldLabel
				attributes={ attributes }
				requiredIndicator={ requiredIndicator }
				handleLabelChange={ handleLabelChange }
			/>

			<input
				type={ fieldType }
				value={ fieldValue }
				name={ fieldName }
				className={ `wp-block-obsidian-form-field__input wp-block-obsidian-form-field__input-${ fieldType }` }
				placeholder={ globalHasPlaceholder && fieldPlaceholder }
				onChange={ ( event ) => setFieldValue( event.target.value ) }
			/>
		</>
	);
};

export default ObsidianFieldInput;
