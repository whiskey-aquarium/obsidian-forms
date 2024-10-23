// Import external dependencies.
import { useState } from '@wordpress/element';

// Import internal dependencies.
import ObsidianFieldLabel from '../components/ObsidianFieldLabel';
import ObsidianFieldDescription from '../components/ObsidianFieldDescription';

/**
 * Component for rendering form settings.
 *
 * @param {Object}   props                      Props passed to the component.
 * @param {Object}   props.attributes           Attributes passed to the component.
 * @param {boolean}  props.globalHasPlaceholder Whether the form has a placeholder.
 * @param {string}   props.requiredIndicator    The required indicator.
 * @param {Function} props.handleLabelChange    Function to handle label change.
 *
 * @return {Object}  The rendered component.
 */
const ObsidianFieldTextarea = ( {
	attributes,
	globalHasPlaceholder,
	requiredIndicator,
	globalDescriptionPlacement,
	handleLabelChange,
	handleDescriptionChange
} ) => {
	const { fieldName, fieldDescription, fieldPlaceholder } = attributes;

	const [ fieldValue, setFieldValue ] = useState( '' );

	return (
		<>
			<ObsidianFieldLabel
				attributes={ attributes }
				requiredIndicator={ requiredIndicator }
				handleLabelChange={ handleLabelChange }
			/>

			{ fieldDescription && globalDescriptionPlacement === 'top' && (
				<ObsidianFieldDescription
					attributes={ attributes }
					handleDescriptionChange={ handleDescriptionChange }
				/>
			) }

			<textarea
				value={ fieldValue }
				name={ fieldName }
				className="wp-block-obsidian-form-field__textarea"
				placeholder={ globalHasPlaceholder && fieldPlaceholder }
				onChange={ ( event ) => setFieldValue( event.target.value ) }
			/>

			{ fieldDescription && globalDescriptionPlacement === 'bottom' && (
				<ObsidianFieldDescription
					attributes={ attributes }
					handleDescriptionChange={ handleDescriptionChange }
				/>
			) }
		</>
	);
};

export default ObsidianFieldTextarea;
