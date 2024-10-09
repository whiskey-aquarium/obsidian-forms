// Import external dependencies.
import { __ } from '@wordpress/i18n';
import { SelectControl, TextControl } from '@wordpress/components';

// Import internal dependencies.
import { fieldTypeOptions } from '../data/FieldTypeOptions';

/**
 * Component for rendering form settings.
 *
 * @param {Object}   props                       Props passed to the component.
 * @param {Object}   props.attributes            Attributes passed to the component.
 * @param {Function} props.handleLabelChange     Function to handle label change.
 * @param {Function} props.handleAttributeChange Function to handle attribute change.
 *
 * @return {Object} The rendered component.
 */
const ObsidianFieldSettings = ( {
	attributes,
	handleLabelChange,
	handleAttributeChange,
} ) => {
	const { fieldType, fieldLabel, fieldName, fieldPlaceholder } = attributes;

	return (
		<>
			<SelectControl
				label={ __( 'Field Type', 'obsidian-forms' ) }
				value={ fieldType }
				options={ fieldTypeOptions.map( ( option ) => ( {
					label: option.label,
					value: option.value,
				} ) ) }
				onChange={ ( value ) =>
					handleAttributeChange( value, 'fieldType' )
				}
			/>

			<TextControl
				label={ __( 'Field Label', 'obsidian-forms' ) }
				value={ fieldLabel }
				onChange={ handleLabelChange }
			/>

			<TextControl
				label={ __( 'Field Name', 'obsidian-forms' ) }
				value={ fieldName }
				onChange={ ( value ) =>
					handleAttributeChange( value, 'fieldName' )
				}
			/>

			<TextControl
				label={ __( 'Field Placeholder', 'obsidian-forms' ) }
				value={ fieldPlaceholder }
				onChange={ ( value ) =>
					handleAttributeChange( value, 'fieldPlaceholder' )
				}
			/>
		</>
	);
};

export default ObsidianFieldSettings;
