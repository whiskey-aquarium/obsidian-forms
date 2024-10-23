// Import external dependencies.
import { useState } from '@wordpress/element';

// Import internal dependencies.
import ObsidianFieldLabel from '../components/ObsidianFieldLabel';
import ObsidianFieldControlsSelect from '../controls/Select';
import ObsidianFieldDescription from '../components/ObsidianFieldDescription';

/**
 * Component for rendering form settings.
 *
 * @param {Object}   props                         Props passed to the component.
 * @param {Object}   props.attributes              Attributes passed to the component.
 * @param {boolean}  props.globalHasPlaceholder    Whether the form has a placeholder.
 * @param {string}   props.requiredIndicator       The required indicator.
 * @param {Function} props.handleLabelChange       Function to handle label change.
 * @param {Function} props.handleFieldOptionChange Function to handle field option change.
 *
 * @return {Object} The rendered component.
 */
const ObsidianFieldSelect = ( {
	attributes,
	globalHasPlaceholder,
	requiredIndicator,
	handleLabelChange,
	globalDescriptionPlacement,
	handleDescriptionChange,
	handleFieldOptionChange,
} ) => {
	const { fieldName, fieldDescription, fieldType, fieldPlaceholder, fieldOptions } = attributes;

	const [ selectedOption, setSelectedOption ] = useState( '' );

	return (
		<>
			<ObsidianFieldControlsSelect
				attributes={ attributes }
				handleFieldOptionChange={ handleFieldOptionChange }
			/>

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

			<select
				value={ selectedOption }
				name={ fieldName }
				placeholder={ globalHasPlaceholder && fieldPlaceholder }
				onChange={ ( event ) =>
					setSelectedOption( event.target.value )
				}
			>
				{ fieldOptions.map( ( option, index ) => {
					// Set to sanitized label if the value is blank
					const maybeLabelAsValue = option.value
						? option.value
						: option.label.replace( /[^a-zA-Z0-9-_]/g, '' );

					return (
						<option key={ index } value={ maybeLabelAsValue }>
							{ option.label }
						</option>
					);
				} ) }
			</select>

			{ fieldDescription && globalDescriptionPlacement === 'bottom' && (
				<ObsidianFieldDescription
					attributes={ attributes }
					handleDescriptionChange={ handleDescriptionChange }
				/>
			) }
		</>
	);
};

export default ObsidianFieldSelect;
