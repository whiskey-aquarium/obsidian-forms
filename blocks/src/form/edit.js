import apiFetch from '@wordpress/api-fetch';
import {
	Button,
	ComboboxControl,
	Notice,
	PanelBody,
	Placeholder,
	TextControl,
} from '@wordpress/components';
import { useEntityBlockEditor, useEntityProp } from '@wordpress/core-data';
import { useDispatch } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { store as noticesStore } from '@wordpress/notices';
import {
	InnerBlocks,
	InspectorControls,
	RichText,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import ObsidianFormSettings from './components/ObsidianFormSettings';
import { getDefaultFormSettings } from './data/FormSettingsMetadata';

function SelectedFormEditor( { formPostId, formSettings, setAttributes } ) {
	const [ blocks, onInput, onChange ] = useEntityBlockEditor(
		'postType',
		'obsidian_form',
		{ id: formPostId }
	);
	const [ title, setTitle ] = useEntityProp(
		'postType',
		'obsidian_form',
		'title',
		formPostId
	);
	const [ meta, setMeta ] = useEntityProp(
		'postType',
		'obsidian_form',
		'meta',
		formPostId
	);

	useEffect( () => {
		setAttributes( {
			formSettings: {
				...getDefaultFormSettings(),
				...( meta?._obsidian_form_settings || {} ),
			},
		} );
	}, [ meta?._obsidian_form_settings, setAttributes ] );

	const blockProps = useBlockProps( {
		className: 'obsidian-form__editor obsidian-form__editor--ready',
	} );
	const innerBlocksProps = useInnerBlocksProps(
		{},
		{
			value: blocks,
			onInput,
			onChange,
			allowedBlocks: [ 'obsidian-form/field-group' ],
			renderAppender: blocks?.length
				? undefined
				: InnerBlocks.ButtonBlockAppender,
		}
	);
	const handleSettingChange = ( key, value ) =>
		setMeta( {
			...meta,
			_obsidian_form_settings: {
				...getDefaultFormSettings(),
				...formSettings,
				[ key ]: value,
			},
		} );

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Obsidian Form Settings', 'obsidian-forms' ) }
				>
					<ObsidianFormSettings
						formSettings={ formSettings }
						handleSettingChange={ handleSettingChange }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<Notice status="info" isDismissible={ false }>
					{ __(
						'This is a shared form. Changes here update every place this form is embedded.',
						'obsidian-forms'
					) }
				</Notice>
				<RichText
					value={ title }
					onChange={ setTitle }
					placeholder={ __( 'Enter Form Title', 'obsidian-forms' ) }
					tagName="h2"
				/>
				<div className="wp-block-obsidian-form-fields">
					<div { ...innerBlocksProps } />
				</div>
			</div>
		</>
	);
}

export default function Edit( { attributes, setAttributes } ) {
	const { formPostId, formSettings } = attributes;
	const [ newFormTitle, setNewFormTitle ] = useState( '' );
	const [ selectedForm, setSelectedForm ] = useState( null );
	const [ availableForms, setAvailableForms ] = useState( [] );
	const [ isLoading, setIsLoading ] = useState( ! formPostId );
	const { createErrorNotice } = useDispatch( noticesStore );

	useEffect( () => {
		if ( formPostId ) {
			return undefined;
		}
		let isCurrent = true;
		setIsLoading( true );
		apiFetch( {
			path: '/wp/v2/obsidian_form?context=edit&per_page=100&_fields=id,title',
		} )
			.then(
				( forms ) =>
					isCurrent &&
					setAvailableForms(
						forms.map( ( form ) => ( {
							label: `${
								form.title.raw ||
								__( 'Untitled', 'obsidian-forms' )
							} (ID: ${ form.id })`,
							value: String( form.id ),
						} ) )
					)
			)
			.catch(
				() =>
					isCurrent &&
					createErrorNotice(
						__(
							'Forms could not be loaded. Refresh and try again.',
							'obsidian-forms'
						),
						{ type: 'snackbar' }
					)
			)
			.finally( () => isCurrent && setIsLoading( false ) );
		return () => {
			isCurrent = false;
		};
	}, [ createErrorNotice, formPostId ] );

	if ( formPostId ) {
		return (
			<SelectedFormEditor
				formPostId={ formPostId }
				formSettings={ formSettings }
				setAttributes={ setAttributes }
			/>
		);
	}

	const createForm = async () => {
		if ( ! newFormTitle.trim() || isLoading ) {
			return;
		}
		setIsLoading( true );
		try {
			const form = await apiFetch( {
				path: '/wp/v2/obsidian_form',
				method: 'POST',
				data: {
					title: newFormTitle.trim(),
					content:
						'<!-- wp:obsidian-form/field-group --><!-- wp:obsidian-form/field {"isRequired":true} /--><!-- /wp:obsidian-form/field-group -->',
					status: 'publish',
					meta: { _obsidian_form_settings: getDefaultFormSettings() },
				},
			} );
			setAttributes( { formPostId: form.id } );
		} catch ( error ) {
			createErrorNotice(
				error?.message ||
					__( 'The form could not be created.', 'obsidian-forms' ),
				{ type: 'snackbar' }
			);
			setIsLoading( false );
		}
	};

	const copyForm = async () => {
		if ( ! selectedForm || isLoading ) {
			return;
		}
		setIsLoading( true );
		try {
			const source = await apiFetch( {
				path: `/wp/v2/obsidian_form/${ selectedForm }?context=edit&_fields=title,content,meta`,
			} );
			const copy = await apiFetch( {
				path: '/wp/v2/obsidian_form',
				method: 'POST',
				data: {
					title: `${
						source.title.raw || __( 'Untitled', 'obsidian-forms' )
					} ${ __( '(Copy)', 'obsidian-forms' ) }`,
					content: source.content.raw,
					status: 'publish',
					meta: {
						_obsidian_form_settings:
							source.meta?._obsidian_form_settings ||
							getDefaultFormSettings(),
					},
				},
			} );
			setAttributes( { formPostId: copy.id } );
		} catch ( error ) {
			createErrorNotice(
				error?.message ||
					__( 'The form could not be copied.', 'obsidian-forms' ),
				{ type: 'snackbar' }
			);
			setIsLoading( false );
		}
	};

	return (
		<Placeholder
			icon="feedback"
			label={ __( 'Obsidian Form', 'obsidian-forms' ) }
			className="obsidian-forms-placeholder"
			instructions={ __(
				'Create a shared form, link an existing one, or copy one before editing it independently.',
				'obsidian-forms'
			) }
		>
			<div className="obsidian-forms-choice">
				<div className="obsidian-forms-choice__form-creator">
					<TextControl
						label={ __( 'New form title', 'obsidian-forms' ) }
						value={ newFormTitle }
						onChange={ setNewFormTitle }
						disabled={ isLoading }
					/>
					<Button
						variant="primary"
						onClick={ createForm }
						disabled={ ! newFormTitle.trim() || isLoading }
						isBusy={ isLoading }
					>
						{ __( 'Create', 'obsidian-forms' ) }
					</Button>
				</div>
				{ availableForms.length > 0 && (
					<div className="obsidian-forms-choice__form-selector">
						<ComboboxControl
							label={ __( 'Existing form', 'obsidian-forms' ) }
							value={ selectedForm }
							options={ availableForms }
							onChange={ setSelectedForm }
							disabled={ isLoading }
						/>
						<div className="button-group">
							<Button
								variant="primary"
								onClick={ () =>
									setAttributes( {
										formPostId: Number( selectedForm ),
									} )
								}
								disabled={ ! selectedForm || isLoading }
							>
								{ __( 'Use shared form', 'obsidian-forms' ) }
							</Button>
							<Button
								variant="secondary"
								onClick={ copyForm }
								disabled={ ! selectedForm || isLoading }
							>
								{ __( 'Copy form', 'obsidian-forms' ) }
							</Button>
						</div>
					</div>
				) }
			</div>
		</Placeholder>
	);
}
