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

const { InspectorControls } = wp.editor;
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
	title: __( 'Glossary' ), // Block title.
	icon: 'book-alt', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	category: 'embed', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
	attributes: {
        primaryColor: {
            type: 'string',
            default: '#0065AE',
		},
		secondaryColor: {
            type: 'string',
            default: '#82878c',
		},
    },
	keywords: [
		__( 'glossary — A simple beautiful glossary' ),
		__( 'glossary' ),
		__( 'Glossary' )
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
		const {
            attributes: {
				primaryColor,
				secondaryColor,
            },
            className,
		} = props;
		
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
							title={__('Primärfarbe')}
							initialOpen={false}
						>
							<ColorPicker
								color={ props.attributes.primaryColor }
								onChangeComplete={ setPrimaryColor }
								disableAlpha
							/>
						</PanelBody>
						<PanelBody
							title={__('Akzentfarbe')}
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
							<h2>A</h2>
						</div>
						</div>
						<div class="sidebar-content">
							<h3 style={{ color: props.attributes.secondaryColor}} >Wählen Sie einen Buchstaben:</h3>
							<div class="letters">
								<span class="active" style={{ color: props.attributes.secondaryColor, borderColor: props.attributes.secondaryColor}}>A</span>
								<span>B</span>
								<span>C</span>
								<span>D</span>
								<span>E</span>
								<span>F</span>
								<span>G</span>
								<span>H</span>
								<span>I</span>
								<span>J</span>
								<span>K</span>
								<span>L</span>
								<span>M</span>
								<span>N</span>
								<span>O</span>
								<span>P</span>
								<span>Q</span>
								<span>R</span>
								<span>S</span>
								<span>T</span>
								<span>U</span>
								<span>V</span>
								<span>W</span>
								<span>X</span>
								<span>Y</span>
								<span>Z</span>
							</div>
						</div>
					</section>
					<main class="content">
						<article class="entry">
						<div class="name">
							<h2 style={{ color: props.attributes.secondaryColor}} >Armenia</h2>
						</div>
						<div class="description">
							<p>
							Lorem Ipsum dolor Lorem Ipsum dolor Lorem Ipsum dolor Lorem Ipsum dolor Lorem Ipsum dolorLorem Ipsum dolor Lorem Ipsum dolorLorem Ipsum dolorLorem Ipsum dolorLorem Ipsum dolorLorem Ipsum dolorLorem Ipsum dolor
							</p>
						</div>
						</article>
						<article class="entry">
						<div class="name">
							<h2 style={{ color: props.attributes.secondaryColor}}>Articulation</h2>
						</div>
						<div class="description">
							<p>
							Lorem Ipsum dolor Lorem Ipsum dolor Lorem Ipsum dolor Lorem Ipsum dolor Lorem Ipsum dolorLorem Ipsum dolor Lorem Ipsum dolorLorem Ipsum dolorLorem Ipsum dolorLorem Ipsum dolorLorem Ipsum dolorLorem Ipsum dolor
							</p>
						</div>
						</article>
						<article class="entry">
						<div class="name">
							<h2 style={{ color: props.attributes.secondaryColor}}>Artisan</h2>
						</div>
						<div class="description">
							<p>
							Lorem Ipsum dolor Lorem Ipsum dolor Lorem Ipsum dolor Lorem Ipsum dolor Lorem Ipsum dolorLorem Ipsum dolor Lorem Ipsum dolorLorem Ipsum dolorLorem Ipsum dolorLorem Ipsum dolorLorem Ipsum dolorLorem Ipsum dolor
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
		return (
			<div className={ props.className }>
				<p>— Hello from the frontend.</p>
				<p>
					CGB BLOCK: <code>glossary</code> is a new Gutenberg block.
				</p>
				<p>
					It was created via{ ' ' }
					<code>
						<a href="https://github.com/ahmadawais/create-guten-block">
							create-guten-block
						</a>
					</code>.
				</p>
			</div>
		);
	},
} );
