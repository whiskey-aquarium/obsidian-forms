import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useEntityProp } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import ObsidianFormSettings from '../form/components/ObsidianFormSettings';
import { getDefaultFormSettings } from '../form/data/FormSettingsMetadata';

function ObsidianFormSettingsSidebar() {
    const postType = useSelect(select => select('core/editor').getCurrentPostType(), []);
    
    if (postType !== 'obsidian_form') {
        return null;
    }

    const [meta, setMeta] = useEntityProp('postType', 'obsidian_form', 'meta');

    const handleSettingChange = (key, value) => {
        const currentSettings = meta?._obsidian_form_settings || getDefaultFormSettings();
        const newSettings = {
            ...currentSettings,
            [key]: value
        };
        
        setMeta({
            ...meta,
            _obsidian_form_settings: newSettings
        });
    };

    return (
        <PluginDocumentSettingPanel
            name="obsidian-form-settings"
            title={__('Form Settings', 'obsidian-forms')}
            opened={true}
        >
            <ObsidianFormSettings
                formSettings={meta?._obsidian_form_settings || getDefaultFormSettings()}
                handleSettingChange={handleSettingChange}
            />
        </PluginDocumentSettingPanel>
    );
}

registerPlugin('obsidian-form-settings', {
    render: ObsidianFormSettingsSidebar
}); 