import { __ } from '@wordpress/i18n';
import { useBlockProps, InnerBlocks, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';

const ALLOWED_BLOCKS = [ 'obsidian-form/field', 'obsidian-form/field-group' ];

const Edit = ( { attributes, setAttributes } ) => {
	const { formId, formName } = attributes;
	const blockProps = useBlockProps();

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<PanelBody title={ __( 'Form Settings', 'obsidian-forms' ) } initialOpen={ true }>
					<TextControl
						label={ __( 'Form ID', 'obsidian-forms' ) }
						value={ formId }
						onChange={ ( value ) => setAttributes( { formId: value } ) }
						help={ __( 'Unique ID for the form.', 'obsidian-forms' ) }
					/>
					<TextControl
						label={ __( 'Form Name', 'obsidian-forms' ) }
						value={ formName }
						onChange={ ( value ) => setAttributes( { formName: value } ) }
						help={ __( 'Name of the form, used for internal reference.', 'obsidian-forms' ) }
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
