import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { createBlock } from '@wordpress/blocks';
import { select, dispatch } from '@wordpress/data';
import { Icon } from '@wordpress/components';
import { plusCircleFilled } from '@wordpress/icons';

/**
 * Edit function for the field group block.
 *
 * @param {Object} props Props passed to the edit component.
 * @return {Object} The rendered edit component.
 */
export default function Edit({ attributes, setAttributes, context, clientId }) {
	const insertFieldBlock = () => {
		const block = createBlock('obsidian-form/field');
		dispatch('core/block-editor').insertBlock(block, undefined, clientId);
	};

	const blockProps = useBlockProps();
	const innerBlocksProps = useInnerBlocksProps(
		blockProps,
		{
			allowedBlocks: ['obsidian-form/field'],
			template: [['obsidian-form/field']],
			renderAppender: () => (
				<Icon
					icon={plusCircleFilled}
					className="wp-block-obsidian-form-field-group__add-field"
					onClick={insertFieldBlock}
				/>
			),
		}
	);

	// Update the form settings attribute when context changes
	if (context['obsidian-form/formSettings'] !== attributes['obsidian-form/formSettings']) {
		setAttributes({
			'obsidian-form/formSettings': context['obsidian-form/formSettings']
		});
	}

	return (
		<div {...blockProps}>
			<div {...innerBlocksProps} />
		</div>
	);
}
