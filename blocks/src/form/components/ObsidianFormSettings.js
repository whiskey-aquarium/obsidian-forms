import {
	TextControl,
	SelectControl,
	RadioControl,
	ToggleControl,
} from '@wordpress/components';

import { getFormSettingsMetadata } from '../data/FormSettingsMetadata';

/**
 * Component for rendering form settings.
 *
 * @param {Object} props Props passed to the component.
 * @return {Object} The rendered component.
 */
const ObsidianFormSettings = ( props ) => {
	const { formSettings, handleSettingChange } = props;
	const metadata = getFormSettingsMetadata();

	return (
		<>
			{ formSettings &&
				// Loop through the formSettings object
				Object.entries( formSettings ).map( ( [ key, value ] ) => {
					const fieldMetadata = metadata[key];
					if (!fieldMetadata) return null;

					// Depending on the 'type', render different controls
					if ( fieldMetadata.type === 'select' ) {
						return (
							<SelectControl
								key={ key }
								label={ fieldMetadata.label }
								value={ value }
								options={ fieldMetadata.options }
								onChange={ ( newValue ) =>
									handleSettingChange( key, newValue )
								}
							/>
						);
					}

					if ( fieldMetadata.type === 'radio' ) {
						return (
							<RadioControl
								key={ key }
								label={ fieldMetadata.label }
								selected={ value }
								options={ fieldMetadata.options }
								onChange={ ( newValue ) =>
									handleSettingChange( key, newValue )
								}
							/>
						);
					}

					if ( fieldMetadata.type === 'toggle' ) {
						return (
							<ToggleControl
								key={ key }
								label={ fieldMetadata.label }
								checked={ value }
								onChange={ ( newValue ) =>
									handleSettingChange( key, newValue )
								}
							/>
						);
					}

					return (
						<TextControl
							key={ key }
							label={ fieldMetadata.label }
							value={ value }
							onChange={ ( newValue ) =>
								handleSettingChange( key, newValue )
							}
						/>
					);
				} ) }
		</>
	);
};

export default ObsidianFormSettings;
