// Import external dependencies.
import { useState } from '@wordpress/element';

// Import internal dependencies.
import ObsidianFieldLabel from '../components/ObsidianFieldLabel';
import ObsidianFieldControlsSelect from '../controls/Select';

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
	handleFieldOptionChange,
} ) => {
	const { fieldName, fieldType, fieldPlaceholder, fieldOptions } = attributes;

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

			<select
				value={ selectedOption }
				name={ fieldName }
				className={ `wp-block-obsidian-form-field__input wp-block-obsidian-form-field__input-${ fieldType }` }
				placeholder={ globalHasPlaceholder && fieldPlaceholder }
				onChange={ ( event ) =>
					setSelectedOption( event.target.value )
				}
			>
				{ fieldOptions.map( ( option, index ) => (
					<option key={ index } value={ option.value }>
						{ option.label }
					</option>
				) ) }
			</select>
		</>
	);
};

export default ObsidianFieldSelect;
