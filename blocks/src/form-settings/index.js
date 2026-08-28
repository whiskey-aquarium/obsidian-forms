import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { useEntityProp } from '@wordpress/core-data';
import { useSelect, useDispatch } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
import ObsidianFormSettings from '../form/components/ObsidianFormSettings';
import { getDefaultFormSettings } from '../form/data/FormSettingsMetadata';
import './editor.scss';

function ObsidianFormSettingsSidebar() {
	const postType = useSelect(
		( select ) => select( 'core/editor' ).getCurrentPostType(),
		[]
	);

	return postType === 'obsidian_form' ? <FormSettingsPanel /> : null;
}

function FormSettingsPanel() {
	const [ meta, setMeta ] = useEntityProp(
		'postType',
		'obsidian_form',
		'meta'
	);
	const { updateBlockAttributes } = useDispatch( 'core/block-editor' );
	const blocks = useSelect(
		( select ) => select( 'core/block-editor' ).getBlocks(),
		[]
	);

	const handleSettingChange = ( key, value ) => {
		const currentSettings =
			meta?._obsidian_form_settings || getDefaultFormSettings();
		const newSettings = {
			...currentSettings,
			[ key ]: value,
		};

		setMeta( {
			...meta,
			_obsidian_form_settings: newSettings,
		} );
	};

	// Update all field-group blocks with the current form settings
	useEffect( () => {
		const formSettings =
			meta?._obsidian_form_settings || getDefaultFormSettings();

		// Find all field-group blocks and update their attributes
		const updateFieldGroupBlocks = ( blockList ) => {
			blockList.forEach( ( block ) => {
				if ( block.name === 'obsidian-form/field-group' ) {
					// Only update if the settings have changed
					if (
						JSON.stringify(
							block.attributes[ 'obsidian-form/formSettings' ]
						) !== JSON.stringify( formSettings )
					) {
						updateBlockAttributes( block.clientId, {
							'obsidian-form/formSettings': formSettings,
						} );
					}
				}
				// Recursively check inner blocks
				if ( block.innerBlocks && block.innerBlocks.length > 0 ) {
					updateFieldGroupBlocks( block.innerBlocks );
				}
			} );
		};

		updateFieldGroupBlocks( blocks );
	}, [ meta?._obsidian_form_settings, blocks, updateBlockAttributes ] );

	return (
		<PluginDocumentSettingPanel
			name="obsidian-form-settings"
			title={ __( 'Form Settings', 'obsidian-forms' ) }
			className="obsidian-form-settings-panel"
		>
			<ObsidianFormSettings
				formSettings={
					meta?._obsidian_form_settings || getDefaultFormSettings()
				}
				handleSettingChange={ handleSettingChange }
			/>
		</PluginDocumentSettingPanel>
	);
}

registerPlugin( 'obsidian-form-settings', {
	render: ObsidianFormSettingsSidebar,
} );
