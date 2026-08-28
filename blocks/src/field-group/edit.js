import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { createBlock } from '@wordpress/blocks';
import { dispatch } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
import { Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { plusCircleFilled } from '@wordpress/icons';

/**
 * Edit function for the field group block.
 *
 * @param {Object}   props               Props passed to the edit component.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Updates block attributes.
 * @param {Object}   props.context       Context inherited from parent blocks.
 * @param {string}   props.clientId      The block client ID.
 * @return {Object} The rendered edit component.
 */
export default function Edit( {
	attributes,
	setAttributes,
	context,
	clientId,
} ) {
	const insertFieldBlock = () => {
		const block = createBlock( 'obsidian-form/field' );
		dispatch( 'core/block-editor' ).insertBlock(
			block,
			undefined,
			clientId
		);
	};

	const blockProps = useBlockProps();
	const formSettings = context[ 'obsidian-form/formSettings' ];
	const storedFormSettings = attributes[ 'obsidian-form/formSettings' ];
	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		allowedBlocks: [ 'obsidian-form/field' ],
		template: [ [ 'obsidian-form/field' ] ],
		renderAppender: () => (
			<Button
				icon={ plusCircleFilled }
				onClick={ insertFieldBlock }
				className="wp-block-obsidian-form-field-group__add-field"
				label={ __( 'Add field', 'obsidian-forms' ) }
			/>
		),
	} );

	// Keep context in sync without updating the block during its render phase.
	useEffect( () => {
		if ( formSettings !== storedFormSettings ) {
			setAttributes( {
				'obsidian-form/formSettings': formSettings,
			} );
		}
	}, [ formSettings, setAttributes, storedFormSettings ] );

	return (
		<div { ...blockProps }>
			<div { ...innerBlocksProps } />
		</div>
	);
}
