import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	useBlockProps,
	RichText,
	BlockControls,
} from '@wordpress/block-editor';
import {
	TextControl,
	SelectControl,
	ToolbarGroup,
	ToolbarButton,
	ToolbarDropdownMenu,
	PanelBody,
	__experimentalUnitControl as UnitControl, // eslint-disable-line @wordpress/no-unsafe-wp-apis
} from '@wordpress/components';
import { textColor, link } from '@wordpress/icons';

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
	const { attributes, setAttributes, context } = props;
	const {
		fieldLabel,
		fieldName,
		fieldType,
		fieldPlaceholder,
		fieldWidth,
		isRequired,
	} = attributes;
	const requiredIndicator =
		context[ 'obsidian-form/formSettings' ].requiredIndicator.value;
	const globalHasPlaceholder =
		context[ 'obsidian-form/formSettings' ].globalHasPlaceholder.value;

	const inputIcon = () => {
		if ( fieldType === 'text' ) {
			return textColor;
		}
		if ( fieldType === 'url' ) {
			return link;
		}
	};

	const handleLabelChange = ( value ) => {
		setAttributes( { fieldLabel: value } );
		setAttributes( {
			fieldName: value.toLowerCase().replace( /\s+/g, '_' ),
		} );
	};

	const flexProperty = fieldWidth !== '' ? `0 0 ${ fieldWidth }` : '1 1 auto';

	const blockProps = useBlockProps( {
		style: {
			flex: flexProperty,
		},
	} );

	return (
		<>
			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						isActive={ isRequired }
						onClick={ () =>
							setAttributes( { isRequired: ! isRequired } )
						}
					>
						{ __( 'Required', 'obsidian-forms' ) }
					</ToolbarButton>

					<ToolbarDropdownMenu
						icon={ inputIcon }
						controls={ [
							{
								icon: textColor,
								title: 'Text',
								onClick: () =>
									setAttributes( { fieldType: 'text' } ),
							},
							{
								icon: link,
								title: 'URL',
								onClick: () =>
									setAttributes( { fieldType: 'url' } ),
							},
						] }
						label="Select a field type"
					/>
				</ToolbarGroup>
			</BlockControls>

			<InspectorControls>
				<PanelBody>
					<SelectControl
						label={ __( 'Field Type', 'obsidian-forms' ) }
						value={ fieldType }
						options={ [
							{ label: 'Text', value: 'text' },
							{ label: 'URL', value: 'url' },
						] }
						onChange={ ( value ) =>
							setAttributes( { fieldType: value } )
						}
					/>

					<TextControl
						label={ __( 'Field Label', 'obsidian-forms' ) }
						value={ fieldLabel }
						onChange={ handleLabelChange }
					/>

					<TextControl
						label={ __( 'Field Name', 'obsidian-forms' ) }
						value={ fieldName }
						onChange={ ( value ) =>
							setAttributes( { fieldName: value } )
						}
					/>

					<TextControl
						label={ __( 'Field Placeholder', 'obsidian-forms' ) }
						value={ fieldPlaceholder }
						onChange={ ( value ) =>
							setAttributes( { fieldPlaceholder: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<InspectorControls group="styles">
				<PanelBody>
					<UnitControl
						label="Field Width"
						value={ fieldWidth }
						onChange={ ( value ) =>
							setAttributes( { fieldWidth: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<label
					className="wp-block-obsidian-form-field__label"
					htmlFor={ fieldName }
				>
					<RichText
						value={ fieldLabel }
						onChange={ handleLabelChange }
						placeholder={ __(
							'Enter Field Label',
							'obsidian-forms'
						) }
						tag="label"
						className="wp-block-obsidian-form-field__label"
					/>
					{ isRequired && <span>{ requiredIndicator }</span> }
				</label>

				<input
					type={ fieldType }
					value=""
					name={ fieldName }
					className={ `wp-block-obsidian-form-field__input wp-block-obsidian-form-field__input-${ fieldType }` }
					placeholder={ globalHasPlaceholder && fieldPlaceholder }
				/>
			</div>
		</>
	);
}
