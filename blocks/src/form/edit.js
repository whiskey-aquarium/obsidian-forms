import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import ObsidianFormSettings from './components/ObsidianFormSettings';

/**
 * Edit function for the obsidian form block. Returns markup for the editor.
 *
 * @param {Object}   props               Properties passed to the function.
 * @param {Object}   props.attributes    Available block attributes.
 * @param {Function} props.setAttributes Function that updates individual attributes.
 *
 * @return {Element} Element to render.
 */
export default function Edit( props ) {
	const { attributes, setAttributes } = props;
	const { formSettings } = attributes;

	const innerBlocksProps = useInnerBlocksProps(
		{},
		{
			allowedBlocks: [ 'obsidian-form/field-group' ],
			template: [
				[ 'obsidian-form/field-group', [ 'obsidian-form/field' ] ],
			],
			renderAppender: () => <InnerBlocks.ButtonBlockAppender />,
		}
	);

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
		<>
			<InspectorControls>
				<PanelBody header="Obsidian Form Settings">
					<ObsidianFormSettings
						formSettings={ formSettings }
						handleSettingChange={ handleSettingChange }
					/>
				</PanelBody>
			</InspectorControls>

			<form { ...useBlockProps() }>
				<RichText
					value={ formSettings.title.value }
					onChange={ ( value ) =>
						handleSettingChange( 'title', value )
					}
					placeholder={ __( 'Enter Form Title', 'obsidian-forms' ) }
					tagName="h2"
				/>

				<div className="wp-block-obsidian-form-fields">
					<div { ...innerBlocksProps }></div>
				</div>
			</form>
		</>
	);
}
