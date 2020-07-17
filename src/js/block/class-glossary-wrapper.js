import { __ } from '@wordpress/i18n'; // Import __() from wp.i18n
import { InspectorControls } from '@wordpress/blockEditor';
import { PanelBody, ColorPicker, Spinner } from '@wordpress/components';
import { Component } from '@wordpress/element';
import Glossary from './frontend-glossary';

class GlossaryWrapper extends Component {
	constructor( props ) {
		super( props );

		this.setPrimaryColor = this.setPrimaryColor.bind(this);
		this.setAccentColor = this.setAccentColor.bind(this);

		this.wrapper      = document.createElement('div');
		this.glossary     = '';
		this.props        = props;
		this.translations = arteeoGlossaryGlobal.translations;
	}
	componentDidMount() {
		let colors = {};
		colors.primary = this.props.attributes.primaryColor;
		colors.accent  = this.props.attributes.accentColor;

		this.glossary = new Glossary(
			this.wrapper,
			colors,
			this.translations,
			arteeoGlossaryGlobal.locale
		);
	}

	componentDidUpdate() {
		let colors = {};
		colors.primary = this.props.attributes.primaryColor;
		colors.accent  = this.props.attributes.accentColor;
		this.glossary.setColors( colors );
		this.glossary.render();
	}
  
	componentWillUnmount() {
	  this.wrapper = null;
	}
  
	render() {
		let className = this.props.className;
		className += ' edit';
		return (
			<div className={className} ref={wrapper => this.wrapper = wrapper} >
				<InspectorControls>
					<PanelBody
						title={ this.translations.primaryColor }
						initialOpen={false}
					>
						<ColorPicker
							color={ this.props.attributes.primaryColor }
							onChangeComplete={ this.setPrimaryColor }
							disableAlpha
						/>
					</PanelBody>
					<PanelBody
						title={this.translations.accentColor}
						initialOpen={false}
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

	setPrimaryColor( color ) {
		this.props.setAttributes( { primaryColor: color === undefined ? '#0065AE' : color.hex } );
	};

	setAccentColor( color ) {
		this.props.setAttributes( { accentColor: color === undefined ? '#0065AE' : color.hex } );
	};
}
export default GlossaryWrapper;