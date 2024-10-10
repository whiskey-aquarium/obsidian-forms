// Import external dependencies.
import { __ } from '@wordpress/i18n';
import { PanelRow, Button, TextControl } from '@wordpress/components';
import { trash, dragHandle } from '@wordpress/icons';
import { ReactSortable } from 'react-sortablejs';

/**
 * Component for rendering form settings.
 *
 * @param {Object}   props                         Props passed to the component.
 * @param {Object}   props.attributes              Attributes passed to the component.
 * @param {Function} props.handleFieldOptionChange Function to handle label change.
 *
 * @return {Object} The rendered component.
 */
const ObsidianFieldControlsFieldOptions = ( {
	attributes,
	handleFieldOptionChange,
} ) => {
	const { fieldType, fieldOptions } = attributes;

	const fieldOptionsList = ( option ) => {
		return (
			<>
				<div className="obsidian-admin-row">
					<div className="obsidian-admin-column obsidian-admin-column--drag-handle">
						<Button
							style={ { marginRight: '10px' } }
							icon={ dragHandle }
						/>
					</div>

					<div className="obsidian-admin-column">
						<TextControl
							label="Label"
							value={ option.label }
							onChange={ ( value ) => {
								const newFieldOptions = fieldOptions.map(
									( _option ) =>
										_option.id === option.id
											? { ..._option, label: value }
											: _option
								);

								handleFieldOptionChange( newFieldOptions );
							} }
						/>
					</div>
					<div className="obsidian-admin-column">
						<TextControl
							label="Value"
							value={ option.value }
							onChange={ ( value ) => {
								const newFieldOptions = fieldOptions.map(
									( _option ) =>
										_option.id === option.id
											? { ..._option, value }
											: _option
								);

								handleFieldOptionChange( newFieldOptions );
							} }
						/>
					</div>

					<div className="obsidian-admin-column obsidian-admin-column--icon">
						<Button
							style={ { marginLeft: '10px' } }
							onClick={ () => {
								const newFieldOptions = fieldOptions.filter(
									( _option ) => _option.id !== option.id
								);

								handleFieldOptionChange( newFieldOptions );
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
			{ fieldType === 'select' ||
			fieldType === 'checkbox' ||
			fieldType === 'radio' ? (
				<>
					<div className="obsidian-admin-row">
						<strong>Field options</strong>
					</div>

					<ReactSortable
						ghostClass="option--ghost"
						handle=".obsidian-admin-column--drag-handle"
						animation={ 200 }
						list={ fieldOptions }
						setList={ ( newState ) =>
							handleFieldOptionChange( newState )
						}
					>
						{ fieldOptions.map( ( option ) => (
							<div className="alignfull" key={ option.id }>
								{ fieldOptionsList( option ) }
							</div>
						) ) }
					</ReactSortable>

					{ /* fieldOptions.map( ( option ) => (
						<div className="alignfull" key={ option.id }>
							{ fieldOptionsList( option ) }
						</div>
					) ) */ }

					<hr />

					<PanelRow>
						<Button
							onClick={ () => {
								const newFieldOptions = [
									...fieldOptions,
									{
										id: Math.random()
											.toString( 36 )
											.substring( 7 ),
										value: '',
										label: '',
									},
								];

								handleFieldOptionChange( newFieldOptions );
							} }
							isPrimary
						>
							{ __( 'Add option', 'obsidian-forms' ) }
						</Button>
					</PanelRow>
				</>
			) : null }
		</>
	);
};

export default ObsidianFieldControlsFieldOptions;
