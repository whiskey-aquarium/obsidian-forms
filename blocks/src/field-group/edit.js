import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { createBlock } from '@wordpress/blocks';
import { select, dispatch } from '@wordpress/data';
import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { useEffect } from '@wordpress/element';
import { Button } from '@wordpress/components';
import { plusCircleFilled } from '@wordpress/icons';

/**
 * Edit function for the field group block.
 *
 * @param {Object} props Props passed to the edit component.
 * @return {Object} The rendered edit component.
 */
export default function Edit( { attributes, setAttributes, context, clientId } ) {
	const insertFieldBlock = () => {
		const block = createBlock( 'obsidian-form/field' );
		dispatch( 'core/block-editor' ).insertBlock( block, undefined, clientId );
	};

	const postType = useSelect( ( select ) => select( 'core/editor' ).getCurrentPostType(), [] );
	const postId = useSelect( ( select ) => select( 'core/editor' ).getCurrentPostId(), [] );

	// Always call useEntityProp but only use the value if we're editing an obsidian_form
	const [ meta ] = useEntityProp( 'postType', 'obsidian_form', 'meta', postId || 0 );

	const blockProps = useBlockProps();
	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		allowedBlocks: [ 'obsidian-form/field' ],
		template: [ [ 'obsidian-form/field' ] ],
		renderAppender: () => (
			<Button
				icon={ plusCircleFilled }
				onClick={ insertFieldBlock }
				className="wp-block-obsidian-form-field-group__add-field"
				label="Add field"
			/>
		),
	} );

	// Update the form settings attribute when context changes (from parent form block)
	useEffect( () => {
		if ( context[ 'obsidian-form/formSettings' ] && context[ 'obsidian-form/formSettings' ] !== attributes[ 'obsidian-form/formSettings' ] ) {
			setAttributes( {
				'obsidian-form/formSettings': context[ 'obsidian-form/formSettings' ],
			} );
		}
	}, [ context[ 'obsidian-form/formSettings' ], attributes[ 'obsidian-form/formSettings' ], setAttributes ] );

	// When editing obsidian_form post type and there's no parent context,
	// initialize from post meta
	useEffect( () => {
		if ( postType === 'obsidian_form' && ! context[ 'obsidian-form/formSettings' ] && meta?._obsidian_form_settings ) {
			setAttributes( {
				'obsidian-form/formSettings': meta._obsidian_form_settings,
			} );
		}
	}, [ postType, context, meta?._obsidian_form_settings, setAttributes ] );

	return (
		<div { ...blockProps }>
			<div { ...innerBlocksProps } />
		</div>
	);
}
