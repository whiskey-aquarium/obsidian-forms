import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	useBlockProps,
	BlockControls,
} from '@wordpress/block-editor';
import {
	TextControl,
	SelectControl,
	ToolbarGroup,
	ToolbarButton,
	ToolbarDropdownMenu,
	PanelBody,
	PanelRow,
	Button,
	__experimentalUnitControl as UnitControl, // eslint-disable-line @wordpress/no-unsafe-wp-apis
} from '@wordpress/components';
import { textColor, link, trash } from '@wordpress/icons';

import ObsidianFieldInput from './components/ObsidianFieldInput';
import ObsidianFieldTextarea from './components/ObsidianFieldTextarea';
import ObsidianFieldSelect from './components/ObsidianFieldSelect';

const fieldTypeOptions = [
	{
		label: 'Text',
		value: 'text',
		component: 'input',
	},
	{
		label: 'URL',
		value: 'url',
		component: 'input',
	},
	{
		label: 'Email',
		value: 'email',
		component: 'input',
	},
	{
		label: 'Number',
		value: 'number',
		component: 'input',
	},
	{
		label: 'Date',
		value: 'date',
		component: 'input',
	},
	{
		label: 'Time',
		value: 'time',
		component: 'input',
	},
	{
		label: 'Textarea',
		value: 'textarea',
		component: 'textarea',
	},
	{
		label: 'Select',
		value: 'select',
		component: 'select',
	},
	{
		label: 'Checkbox',
		value: 'checkbox',
		component: 'input',
	},
	{
		label: 'Radio',
		value: 'radio',
		component: 'input',
	},
	{
		label: 'File',
		value: 'file',
		component: 'input',
	},
	{
		label: 'Submit',
		value: 'submit',
		component: 'input',
	},
	{
		label: 'Reset',
		value: 'reset',
		component: 'input',
	},
	{
		label: 'Hidden',
		value: 'hidden',
		component: 'input',
	},
	{
		label: 'Password',
		value: 'password',
		component: 'input',
	},
	{
		label: 'Phone',
		value: 'tel',
		component: 'input',
	},
	{
		label: 'Range',
		value: 'range',
		component: 'input',
	},
];

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
		fieldOptions,
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

	const fieldOptionsList = ( option ) => {
		return (
			<>
				<div className="obsidian-admin-row">
					<div className="obsidian-admin-column">
						<TextControl
							label="Value"
							value={ option.value }
							onChange={ ( value ) => {
								setAttributes( {
									fieldOptions: fieldOptions.map(
										( _option ) =>
											_option.id === option.id
												? { ..._option, value }
												: _option
									),
								} );
							} }
						/>
					</div>

					<div className="obsidian-admin-column">
						<TextControl
							label="Label"
							value={ option.label }
							onChange={ ( value ) => {
								setAttributes( {
									fieldOptions: fieldOptions.map(
										( _option ) =>
											_option.id === option.id
												? { ..._option, label: value }
												: _option
									),
								} );
							} }
						/>
					</div>

					<div className="obsidian-admin-column obsidian-admin-column--icon">
						<Button
							style={ { marginLeft: '10px' } }
							onClick={ () => {
								setAttributes( {
									fieldOptions: fieldOptions.filter(
										( _option ) => _option.id !== option.id
									),
								} );
							} }
							isPrimary
							icon={ trash }
						/>
					</div>
				</div>
			</>
		);
	};

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
						options={ fieldTypeOptions.map( ( option ) => ( {
							label: option.label,
							value: option.value,
						} ) ) }
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

					{ fieldType === 'select' && (
						<>
							<div className="obsidian-admin-row">
								<strong>Field options</strong>
							</div>

							{ fieldOptions.map( ( option ) => (
								<div className="alignfull" key={ option.id }>
									{ fieldOptionsList( option ) }
								</div>
							) ) }

							<hr />

							<PanelRow>
								<Button
									onClick={ () => {
										setAttributes( {
											fieldOptions: [
												...fieldOptions,
												{
													id: Math.random()
														.toString( 36 )
														.substring( 7 ),
													value: '',
													label: '',
												},
											],
										} );
									} }
									isPrimary
								>
									{ __( 'Add option', 'wkkf-theme' ) }
								</Button>
							</PanelRow>
						</>
					) }
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
				{ fieldTypeOptions.filter(
					( option ) => option.value === fieldType
				)[ 0 ].component === 'input' && (
					<ObsidianFieldInput
						attributes={ attributes }
						globalHasPlaceholder={ globalHasPlaceholder }
						requiredIndicator={ requiredIndicator }
						handleLabelChange={ handleLabelChange }
					/>
				) }

				{ fieldTypeOptions.filter(
					( option ) => option.value === fieldType
				)[ 0 ].component === 'textarea' && (
					<ObsidianFieldTextarea
						attributes={ attributes }
						globalHasPlaceholder={ globalHasPlaceholder }
						requiredIndicator={ requiredIndicator }
						handleLabelChange={ handleLabelChange }
					/>
				) }

				{ fieldTypeOptions.filter(
					( option ) => option.value === fieldType
				)[ 0 ].component === 'select' && (
					<ObsidianFieldSelect
						attributes={ attributes }
						globalHasPlaceholder={ globalHasPlaceholder }
						requiredIndicator={ requiredIndicator }
						handleLabelChange={ handleLabelChange }
					/>
				) }
			</div>
		</>
	);
}
