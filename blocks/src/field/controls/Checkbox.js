// Import external dependencies.
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';

// Import internal dependencies.
import ObsidianFieldControlsFieldOptions from './FieldOptions';

/**
 * Component for rendering form settings.
 *
 * @param {Object}   props                         Props passed to the component.
 * @param {Object}   props.attributes              Attributes passed to the component.
 * @param {Function} props.handleExtraPropsChange  Function to handle extra props change.
 * @param {Function} props.handleFieldOptionChange Function to handle field option
 *
 * @return {Object} The rendered component.
 */
const ObsidianFieldControlsCheckbox = ( {
	attributes,
	handleExtraPropsChange,
	handleFieldOptionChange,
} ) => {
	const { extraProps } = attributes;

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Checkbox Settings', 'obsidian-forms' ) }
				>
					<SelectControl
						label={ __( 'Checkboxes Layout', 'obsidian-forms' ) }
						value={
							extraProps.checkboxesLayout
								? extraProps.checkboxesLayout
								: 'stacked'
						}
						options={ [
							{
								label: __( 'Stacked', 'obsidian-forms' ),
								value: 'stacked',
							},
							{
								label: __( 'Row', 'obsidian-forms' ),
								value: 'row',
							},
						] }
						onChange={ ( value ) => {
							handleExtraPropsChange( value, 'checkboxesLayout' );
						} }
					/>

					<ObsidianFieldControlsFieldOptions
						attributes={ attributes }
						handleFieldOptionChange={ handleFieldOptionChange }
					/>
				</PanelBody>
			</InspectorControls>
		</>
	);
};

export default ObsidianFieldControlsCheckbox;
