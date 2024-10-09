import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import Edit from './edit';
import './style.scss';
import './editor.scss';

import metadata from './block.json';

registerBlockType( metadata.name, {
	...metadata,
	edit: Edit,
	save: () => {
		useBlockProps.save();

		return null;
	},
} );
