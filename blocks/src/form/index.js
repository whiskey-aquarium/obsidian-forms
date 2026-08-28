import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import './style.scss';
import './editor.scss';

import metadata from './block.json';
import { obsidianFormsIcon } from './icon';

registerBlockType( metadata.name, {
	...metadata,
	icon: obsidianFormsIcon,
	edit: Edit,
	save: () => null,
} );
