import apiFetch from '@wordpress/api-fetch';
import { ComboboxControl, Button, Placeholder, TextControl, PanelBody } from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { select } from '@wordpress/data';
import { useBlockProps, useInnerBlocksProps, InnerBlocks, RichText, InspectorControls } from '@wordpress/block-editor';
import { useEntityBlockEditor, useEntityProp } from '@wordpress/core-data';
import ObsidianFormSettings from './components/ObsidianFormSettings';

export default function Edit( props ) {
	const { attributes, setAttributes } = props;
	const { formPostId, formSettings } = attributes;

	// State to manage form creation and selection
	const [isCreatingNew, setIsCreatingNew] = useState(false);
	const [newFormTitle, setNewFormTitle] = useState('');
	const [selectedForm, setSelectedForm] = useState(null);
	const [availableForms, setAvailableForms] = useState([]);
	const [isLoadingForms, setIsLoadingForms] = useState(true);
	const [noFormsAvailable, setNoFormsAvailable] = useState(false);
	const [blocks, onInput, onChange] = useEntityBlockEditor('postType', 'obsidian_form', { id: formPostId || 0 });
	const [title, setTitle] = useEntityProp('postType', 'obsidian_form', 'title', formPostId || 0);
	const blockProps = useBlockProps({className: 'obsidian-form__editor obsidian-form__editor--ready'});

	const innerBlocksProps = useInnerBlocksProps({}, {
		value: blocks,
		onInput,
		onChange,
		allowedBlocks: ['obsidian-form/field-group'],
		renderAppender: blocks?.length ? undefined : InnerBlocks.ButtonBlockAppender,
	});

	const fetchAllForms = async () => {
		let page = 1;
		let allForms = [];
		let totalPages = 1;

		while (page <= totalPages) {
			try {
				const response = await apiFetch({
					path: `/wp/v2/obsidian_form?per_page=100&page=${page}`,
					parse: false,
				});

				// Parse the JSON body
				const forms = await response.json();

				if (forms.length > 0) {
					allForms = allForms.concat(forms);
					// Update totalPages based on the header information
					totalPages = parseInt(response.headers.get('X-WP-TotalPages'), 10);
					page++;
				} else {
					break;
				}
			} catch (error) {
				break;
			}
		}

		return allForms;
	};

	useEffect(() => {
		if (!formPostId) {
			setIsLoadingForms(true);
			fetchAllForms()
				.then((forms) => {
					if (forms.length === 0) {
						setNoFormsAvailable(true);
					} else {
						const options = forms.map((form) => ({
							label: `${form.title.rendered} (ID: ${form.id})`,
							value: form.id,
						}));
						setAvailableForms(options);
						setNoFormsAvailable(false);
					}
				})
				.finally(() => {
					setIsLoadingForms(false);
				});
		}
	}, [formPostId]);

	const handleCreateNewForm = async () => {
		if (newFormTitle.trim()) {
			// Create a new form using the REST API
			const createdForm = await apiFetch({
				path: '/wp/v2/obsidian_form',
				method: 'POST',
				data: { 
					title: newFormTitle, 
					content: `<!-- wp:obsidian-form/field-group -->
<!-- wp:obsidian-form/field {"isRequired":true} /-->
<!-- /wp:obsidian-form/field-group -->`,
					status: 'publish',
				}
			});

			if (createdForm?.id) {
				setAttributes({ formPostId: createdForm.id });
				setIsCreatingNew(false);
			}
		}
	};

	const handleSelectExistingForm = () => {
		if (selectedForm) {
			setAttributes({ formPostId: selectedForm });
		}
	};

	const handleCopyExistingForm = async () => {
		if (selectedForm) {
			try {
				const formToCopy = await apiFetch({
					path: `/wp/v2/obsidian_form/${selectedForm}`,
				});

				if (formToCopy?.id) {
					const newForm = await apiFetch({
						path: '/wp/v2/obsidian_form',
						method: 'POST',
						data: {
							title: `${formToCopy.title.rendered} (Copy)`,
							content: formToCopy.raw_content,
							status: 'publish',
						},
					});

					if (newForm?.id) {
						setAttributes({ formPostId: newForm.id });
					}
				}
			} catch (error) {
				console.error('Error copying form:', error);
				// TODO: Show the error notice in the UI
			}
		}
	};

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

	if (formPostId) {
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

				<form { ...blockProps }>
					<RichText
						value={title}
						onChange={setTitle}
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

	// Render UI to create or select a form if no formPostId is set
	return (
		<Placeholder
			icon="feedback"
			label={__('Obsidian Form', 'obsidian-forms')}
			instructions={
				noFormsAvailable
					? __('Create a new form to get started!', 'obsidian-forms')
					: __('Create a new form or select an existing one.', 'obsidian-forms')
			}
		>
			<div className="obsidian-forms-choice">
				<div className="obsidian-forms-choice__form-creator">
					<TextControl
						placeholder={__('New form title…', 'obsidian-forms')}
						value={newFormTitle}
						onChange={setNewFormTitle}
					/>
					<Button
						isPrimary
						onClick={handleCreateNewForm}
						disabled={!newFormTitle.trim()}
					>
						{__('Create', 'obsidian-forms')}
					</Button>
				</div>

				{!noFormsAvailable && (
					<div className="obsidian-forms-choice__form-selector">
						<ComboboxControl
							value={selectedForm}
							options={availableForms}
							onChange={(value) => setSelectedForm(value)}
							shouldShowMenuOnFocus={true}
							disabled={isLoadingForms}
						/>
						<div className="button-group">
							<Button
								isPrimary
								onClick={handleSelectExistingForm}
								disabled={!selectedForm || isLoadingForms}
							>
								{__('Select', 'obsidian-forms')}
							</Button>
							<Button
								className="is-secondary"
								onClick={handleCopyExistingForm}
								disabled={!selectedForm || isLoadingForms}
							>
								{__('Copy', 'obsidian-forms')}
							</Button>
						</div>
					</div>
				)}
			</div>
		</Placeholder>
	);
}
