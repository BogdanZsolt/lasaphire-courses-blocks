import { registerBlockType } from '@wordpress/blocks';
import { ReactComponent as Logo } from '../../ls-logo.svg';
import './style.scss';
import Edit from './edit';
import metadata from './block.json';

registerBlockType( metadata.name, {
	icon: { src: Logo },
	edit: Edit,
	save: () => {
		return null;
	},
} );
