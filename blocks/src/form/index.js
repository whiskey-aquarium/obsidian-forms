import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';
import './style.scss';
import './editor.scss';

import metadata from './block.json';

registerBlockType( metadata.name, {
	...metadata,
	edit: Edit,
	save,
} );
