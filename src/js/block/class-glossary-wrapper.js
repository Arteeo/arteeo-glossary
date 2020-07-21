import { InspectorControls } from '@wordpress/blockEditor';
import { PanelBody, ColorPicker } from '@wordpress/components';
import { Component } from '@wordpress/element';
import Glossary from './class-glossary';

/**
 * Glossary Wrapper
 *
 * Wraps the common js glossary class inside react in order to support InspectorControls.
 *
 * @since 1.0.0
 */
/*global arteeoGlossaryGlobal */
class GlossaryWrapper extends Component {
	/**
	 * Constructor
	 *
	 * Constructs the react wrapper component. Also does some binding in order for callbacks to be able to call
	 * functions.
	 *
	 * @since 1.0.0
	 * @constructs
	 * @param {Object} props the properties and arguments of the component.
	 */
	constructor( props ) {
		super( props );

		this.setPrimaryColor = this.setPrimaryColor.bind( this );
		this.setAccentColor = this.setAccentColor.bind( this );

		this.wrapper = document.createElement( 'div' );
		this.glossary = '';
		this.props = props;
		this.translations = arteeoGlossaryGlobal.translations;
	}

	/**
	 * Mount
	 *
	 * Called when the component is mounted. Sets the colors and initialises the common-js glossary.
	 *
	 * @since 1.0.0
	 */
	componentDidMount() {
		const colors = {};
		colors.primary = this.props.attributes.primaryColor;
		colors.accent = this.props.attributes.accentColor;

		this.glossary = new Glossary(
			this.wrapper,
			colors,
			this.translations,
			arteeoGlossaryGlobal.locale
		);
	}

	/**
	 * Update
	 *
	 * Called when the component updates. Adjusts the colors of the common-js glossary and renders it again.
	 *
	 * @since 1.0.0
	 */
	componentDidUpdate() {
		const colors = {};
		colors.primary = this.props.attributes.primaryColor;
		colors.accent = this.props.attributes.accentColor;
		this.glossary.setColors( colors );
		this.glossary.render();
	}

	/**
	 * Unmount
	 *
	 * Called when the component unmounts. Empties the wrapper object.
	 *
	 * @since 1.0.0
	 */
	componentWillUnmount() {
		this.wrapper = null;
	}

	/**
	 * Render
	 *
	 * Renders the wrapper object. Generates a simple div which is then injected with the commmon-js component.
	 *
	 * @since 1.0.0
	 */
	render() {
		let className = this.props.className;
		className += ' edit';
		return (
			<div
				className={ className }
				ref={ ( wrapper ) => ( this.wrapper = wrapper ) }
			>
				<InspectorControls>
					<PanelBody
						title={ this.translations.primaryColor }
						initialOpen={ false }
					>
						<ColorPicker
							color={ this.props.attributes.primaryColor }
							onChangeComplete={ this.setPrimaryColor }
							disableAlpha
						/>
					</PanelBody>
					<PanelBody
						title={ this.translations.accentColor }
						initialOpen={ false }
					>
						<ColorPicker
							color={ this.props.attributes.accentColor }
							onChangeComplete={ this.setAccentColor }
							disableAlpha
						/>
					</PanelBody>
				</InspectorControls>
			</div>
		);
	}

	/**
	 * Set primary color
	 *
	 * Sets the primary color attribute of the component.
	 *
	 * @since 1.0.0
	 * @param {string} color The color which should be set for primary.
	 */
	setPrimaryColor( color ) {
		this.props.setAttributes( {
			primaryColor: color === undefined ? '#0065AE' : color.hex,
		} );
	}

	/**
	 * Set accent color
	 *
	 * Sets the accemt color attribute of the component.
	 *
	 * @since 1.0.0
	 * @param {string} color The color which should be set for accent.
	 */
	setAccentColor( color ) {
		this.props.setAttributes( {
			accentColor: color === undefined ? '#0065AE' : color.hex,
		} );
	}
}
export default GlossaryWrapper;
