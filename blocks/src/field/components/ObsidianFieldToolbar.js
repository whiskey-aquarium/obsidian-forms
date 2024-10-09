// Import external dependencies.
import { __ } from '@wordpress/i18n';
import {
	ToolbarGroup,
	ToolbarButton,
	ToolbarDropdownMenu,
} from '@wordpress/components';
import { textHorizontal } from '@wordpress/icons';

// Import internal dependencies.
import { fieldTypeOptions } from '../data/FieldTypeOptions';

/**
 * Component for rendering form settings.
 *
 * @param {Object}   props                       Props passed to the component.
 * @param {Object}   props.attributes            Attributes passed to the component.
 * @param {Function} props.handleAttributeChange Function to handle label change.
 *
 * @return {Object} The rendered component.
 */
const ObsidianFieldToolbar = ( { attributes, handleAttributeChange } ) => {
	const { isRequired } = attributes;

	return (
		<>
			<ToolbarGroup>
				<ToolbarButton
					isActive={ isRequired }
					onClick={ () =>
						handleAttributeChange( ! isRequired, 'isRequired' )
					}
				>
					{ __( 'Required', 'obsidian-forms' ) }
				</ToolbarButton>

				<ToolbarDropdownMenu
					icon={ textHorizontal }
					controls={ fieldTypeOptions.map( ( option ) => ( {
						icon: option.icon ? option.icon : textHorizontal,
						title: option.label,
						onClick: () =>
							handleAttributeChange( option.value, 'fieldType' ),
					} ) ) }
					label={ __( 'Select a field type', 'obsidian-forms' ) }
				/>
			</ToolbarGroup>
		</>
	);
};

export default ObsidianFieldToolbar;
