/**
 * Glossary block
 *
 * Registers the block with WordPress
 *
 * @since 1.0.0
 */
/*global arteeoGlossaryGlobal */
import '../../stylesheets/main.scss';
import GlossaryWrapper from './class-glossary-wrapper';
import { registerBlockType } from '@wordpress/blocks';

/**
 * Register: aa Gutenberg Block.
 *
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made editor as an option to any
 * editor interface where blocks are implemented.
 *
 * @since 1.0.0
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
registerBlockType( 'arteeo/glossary-block', {
	title: arteeoGlossaryGlobal.translations.Glossary,
	icon: 'book-alt',
	category: 'embed',
	attributes: {
		primaryColor: {
			type: 'string',
			default: '#0065AE',
		},
		accentColor: {
			type: 'string',
			default: '#82878c',
		},
	},
	keywords: [
		arteeoGlossaryGlobal.translations.glossaryDescription,
		arteeoGlossaryGlobal.translations.glossary,
		arteeoGlossaryGlobal.translations.Glossary,
	],
	/**
	 * Edit function
	 *
	 * The edit function generates the Block inside the backend and allows changes to the attributes by the user.
	 *
	 * @since 1.0.0
	 * @param {Object} props Props.
	 * @return {Object} JSX Component.
	 *
	 */
	edit: ( props ) => {
		return <GlossaryWrapper { ...props } />;
	},

	/**
	 * Save function
	 *
	 * The save function generates the HTML which will be shown in the frontend.
	 *
	 * @since 1.0.0
	 * @param {Object} props Props.
	 * @return {Object} JSX Frontend HTML.
	 */
	save: ( props ) => {
		return (
			<div
				className={ props.className }
				data-primary-color={ props.attributes.primaryColor }
				data-accent-color={ props.attributes.accentColor }
			></div>
		);
	},
} );
