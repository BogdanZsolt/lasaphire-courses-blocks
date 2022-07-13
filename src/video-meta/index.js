import { registerBlockType } from '@wordpress/blocks';
import './style.scss';
import Edit from './edit';
import save from './save';
import metadata from './block.json';

// Import the logo
import { ReactComponent as Logo } from '../../ls-logo.svg';

registerBlockType( metadata.name, {
	icon: { src: Logo },
	edit: Edit,
	save,
} );
