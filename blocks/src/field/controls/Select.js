// Import external dependencies.
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';

// Import internal dependencies.
import ObsidianFieldControlsFieldOptions from './FieldOptions';

/**
 * Component for rendering form settings.
 *
 * @param {Object}   props                         Props passed to the component.
 * @param {Object}   props.attributes              Attributes passed to the component.
 * @param {Function} props.handleFieldOptionChange Function to handle field option
 *
 * @return {Object} The rendered component.
 */
const ObsidianFieldControlsSelect = ( {
	attributes,
	handleFieldOptionChange,
} ) => {
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Select Settings', 'obsidian-forms' ) }>
					<ObsidianFieldControlsFieldOptions
						attributes={ attributes }
						handleFieldOptionChange={ handleFieldOptionChange }
					/>
				</PanelBody>
			</InspectorControls>
		</>
	);
};

export default ObsidianFieldControlsSelect;
