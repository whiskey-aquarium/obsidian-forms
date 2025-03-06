/**
 * Gets the form settings metadata from the window object.
 * 
 * @return {Object} The form settings metadata.
 */
export const getFormSettingsMetadata = () => window.obsidianForms?.settings?.metadata ?? {};

/**
 * Gets the default form settings from the window object.
 * 
 * @return {Object} The default form settings.
 */
export const getDefaultFormSettings = () => window.obsidianForms?.settings?.defaults ?? {};

/**
 * Checks if the form settings are available.
 * 
 * @return {boolean} True if settings are available, false otherwise.
 */
export const isSettingsAvailable = () => {
    return Boolean(window.obsidianForms?.settings);
}; 