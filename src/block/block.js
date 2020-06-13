/**
 * BLOCK: glossary
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

//  Import CSS.
import './editor.scss';
import './style.scss';

const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks

const { InspectorControls } = wp.blockEditor;
const { PanelBody, ColorPicker } = wp.components;

/**
 * Register: aa Gutenberg Block.
 *
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made editor as an option to any
 * editor interface where blocks are implemented.
 *
 * @link https://wordpress.org/gutenberg/handbook/block-api/
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
registerBlockType( 'glossary/block-glossary', {
	// Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
	title: cgbGlobal.__Glossary, // Block title.
	icon: 'book-alt', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	category: 'embed', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
	keywords: [
		cgbGlobal.__glossaryDescription,
		cgbGlobal.__glossary,
		cgbGlobal.__Glossary,
	],
	/**
	 * The edit function describes the structure of your block in the context of the editor.
	 * This represents what the editor will render when the block is used.
	 *
	 * The "edit" property must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 *
	 * @param {Object} props Props.
	 * @returns {Mixed} JSX Component.
	 */
	edit: ( props ) => {
		const activeLetter = (letter) => {
			if(letter == cgbGlobal.__example.slice(0, 1).toUpperCase()) {
				return true;
			}			
			return false;
		}

		const setPrimaryColor = ( color ) => {
			props.setAttributes( { primaryColor: color === undefined ? '#0065AE' : color.hex } );
		};

		const setSecondaryColor = ( color ) => {
			props.setAttributes( { secondaryColor: color === undefined ? '#0065AE' : color.hex } );
		};

		const letters = [];

		for (var i = 65; i <= 90; i++) {
				if(activeLetter(String.fromCharCode(i))) {
					letters.push(<a class="active" style={{ color: props.attributes.secondaryColor, borderColor: props.attributes.secondaryColor}}>{String.fromCharCode(i)}</a>);
				} else {
					letters.push(<a>{String.fromCharCode(i)}</a>)
				}
		}

		// Creates a <div class='wp-block-glossary-block-glossary'></siv>.
		return (
			<div className={ props.className }>
				{
					<InspectorControls>
						<PanelBody
							title={cgbGlobal.__primaryColor}
							initialOpen={false}
						>
							<ColorPicker
								color={ props.attributes.primaryColor }
								onChangeComplete={ setPrimaryColor }
								disableAlpha
							/>
						</PanelBody>
						<PanelBody
							title={cgbGlobal.__accentColor}
							initialOpen={false}
						>
							<ColorPicker
								color={ props.attributes.secondaryColor }
								onChangeComplete={ setSecondaryColor }
								disableAlpha
							/>
						</PanelBody>
					</InspectorControls>
				}
				<div class="wrapper"> 
					<section class="sidebar">
						<div class="sidebar-header" style={{ backgroundColor: props.attributes.primaryColor}}>
						<div class="letter">
							<h2>{cgbGlobal.__example.slice(0, 1).toUpperCase()}</h2>
						</div>
						</div>
						<div class="sidebar-content">
							<h3 style={{ color: props.attributes.secondaryColor}} >{cgbGlobal.__selectLetter}</h3>
							<div class="letters">
								{letters}
							</div>
						</div>
					</section>
					<main class="content">
						<article class="entry">
						<div class="name">
							<h2 style={{ color: props.attributes.secondaryColor}} >{cgbGlobal.__example} 1</h2>
						</div>
						<div class="description">
							<p>
								Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam semper, nisl et ornare lacinia, metus quam sodales dui, sed convallis lacus nunc ac nisi. Aliquam mattis nisi ut nulla vehicula, et feugiat tellus sollicitudin. Suspendisse potenti. Suspendisse bibendum erat eu elit sodales vehicula. Duis vitae mi nibh. Fusce scelerisque fermentum ornare. Proin aliquet egestas tellus nec euismod. 
							</p>
						</div>
						</article>
						<article class="entry">
						<div class="name">
							<h2 style={{ color: props.attributes.secondaryColor}}>{cgbGlobal.__example} 2</h2>
						</div>
						<div class="description">
							<p>
								Lorem ipsum dolor sit amet. 
							</p>
						</div>
						</article>
						<article class="entry">
						<div class="name">
							<h2 style={{ color: props.attributes.secondaryColor}}>{cgbGlobal.__example} 3</h2>
						</div>
						<div class="description">
							<p>
								Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut accumsan nunc in lectus pretium tempus. Sed eget maximus ex. Aliquam.
							</p>
						</div>
						</article>
					</main>
				</div>
			</div>
		);
	},
	/**
	 * The save function defines the way in which the different attributes should be combined
	 * into the final markup, which is then serialized by Gutenberg into post_content.
	 *
	 * The "save" property must be specified and must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 *
	 * @param {Object} props Props.
	 * @returns {Mixed} JSX Frontend HTML.
	 */
	save: ( props ) => {
		// Enable server side rendering
		return null;
	},
} );