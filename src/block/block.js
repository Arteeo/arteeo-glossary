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
registerBlockType( 'cgb/block-glossary', {
	// Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
	title: __( 'Glossary', 'glossary' ), // Block title.
	icon: 'book-alt', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	category: 'embed', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
	keywords: [
		__( 'glossary — A simple beautiful glossary', 'glossary' ),
		__( 'glossary', 'glossary' ),
		__( 'Glossary', 'glossary' )
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
		const setPrimaryColor = ( color ) => {
			props.setAttributes( { primaryColor: color === undefined ? '#0065AE' : color.hex } );
		};

		const setSecondaryColor = ( color ) => {
			props.setAttributes( { secondaryColor: color === undefined ? '#0065AE' : color.hex } );
		};

		// Creates a <div class='wp-block-cgb-block-glossary'></siv>.
		return (
			<div className={ props.className }>
				{
					<InspectorControls>
						<PanelBody
							title={__( 'Primary color', 'glossary' )}
							initialOpen={false}
						>
							<ColorPicker
								color={ props.attributes.primaryColor }
								onChangeComplete={ setPrimaryColor }
								disableAlpha
							/>
						</PanelBody>
						<PanelBody
							title={__( 'Accent color', 'glossary' )}
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
							<h2>E</h2>
						</div>
						</div>
						<div class="sidebar-content">
							<h3 style={{ color: props.attributes.secondaryColor}} >{__('Select a letter:', 'glossary')}</h3>
							<div class="letters">
								<a>A</a>
								<a>B</a>
								<a>C</a>
								<a>D</a>
								<a class="active" style={{ color: props.attributes.secondaryColor, borderColor: props.attributes.secondaryColor}}>E</a>
								<a>F</a>
								<a>G</a>
								<a>H</a>
								<a>I</a>
								<a>J</a>
								<a>K</a>
								<a>L</a>
								<a>M</a>
								<a>N</a>
								<a>O</a>
								<a>P</a>
								<a>Q</a>
								<a>R</a>
								<a>S</a>
								<a>T</a>
								<a>U</a>
								<a>V</a>
								<a>W</a>
								<a>X</a>
								<a>Y</a>
								<a>Z</a>
							</div>
						</div>
					</section>
					<main class="content">
						<article class="entry">
						<div class="name">
							<h2 style={{ color: props.attributes.secondaryColor}} >{__('Example', 'glossary')} 1</h2>
						</div>
						<div class="description">
							<p>
								Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam semper, nisl et ornare lacinia, metus quam sodales dui, sed convallis lacus nunc ac nisi. Aliquam mattis nisi ut nulla vehicula, et feugiat tellus sollicitudin. Suspendisse potenti. Suspendisse bibendum erat eu elit sodales vehicula. Duis vitae mi nibh. Fusce scelerisque fermentum ornare. Proin aliquet egestas tellus nec euismod. 
							</p>
						</div>
						</article>
						<article class="entry">
						<div class="name">
							<h2 style={{ color: props.attributes.secondaryColor}}>{__('Example', 'glossary')} 2</h2>
						</div>
						<div class="description">
							<p>
								Lorem ipsum dolor sit amet. 
							</p>
						</div>
						</article>
						<article class="entry">
						<div class="name">
							<h2 style={{ color: props.attributes.secondaryColor}}>{__('Example', 'glossary')} 3</h2>
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