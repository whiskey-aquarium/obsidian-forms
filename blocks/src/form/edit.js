import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InnerBlocks,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import ObsidianFormSettings from './components/ObsidianFormSettings';

const ALLOWED_BLOCKS = [ 'obsidian-form/field', 'obsidian-form/field-group' ];

/**
 * Edit function for the form block.
 *
 * @param {Object} props Props passed to the edit component.
 * @return {Object} The rendered edit component.
 */
const Edit = ( props ) => {
	const { attributes, setAttributes } = props;
	const { formSettings } = attributes;
	const blockProps = useBlockProps();

	const handleSettingChange = ( key, value ) => {
		const newSettings = {
			...formSettings,
			[ key ]: {
				...formSettings[ key ],
				value,
			},
		};
		setAttributes( { formSettings: newSettings } );
	};

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<PanelBody
					title={ __( 'Form Settings', 'obsidian-forms' ) }
					initialOpen={ true }
				>
					<ObsidianFormSettings
						formSettings={ formSettings }
						handleSettingChange={ handleSettingChange }
					/>
				</PanelBody>
			</InspectorControls>
			<form>
				<InnerBlocks allowedBlocks={ ALLOWED_BLOCKS } />
			</form>
		</div>
	);
};

export default Edit;
