import { __ } from '@wordpress/i18n'; // Import __() from wp.i18n
import { InspectorControls } from '@wordpress/blockEditor';
import { PanelBody, ColorPicker, Spinner } from '@wordpress/components';
import { Component } from '@wordpress/element';
import Glossary from './frontend-glossary';

class GlossaryWrapper extends Component {
	constructor( props ) {
		super( props );

		this.setPrimaryColor = this.setPrimaryColor.bind(this);
		this.setSecondaryColor = this.setSecondaryColor.bind(this);

		this.wrapper  = document.createElement('div');
		this.glossary = '';
		this.props    = props;
	}
	componentDidMount() {
		this.glossary = new Glossary(
			this.wrapper,
			this.props.attributes.primaryColor,
			this.props.attributes.secondaryColor,
			cgbGlobal.__selectLetter,
			cgbGlobal.locale
		);
	}

	componentDidUpdate() {
		console.log(this.props.name, ": componentDidUpdate()");
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
						title={cgbGlobal.__primaryColor}
						initialOpen={false}
					>
						<ColorPicker
							color={ this.props.attributes.primaryColor }
							onChangeComplete={ this.setPrimaryColor }
							disableAlpha
						/>
					</PanelBody>
					<PanelBody
						title={cgbGlobal.__accentColor}
						initialOpen={false}
					>
						<ColorPicker
							color={ this.props.attributes.secondaryColor }
							onChangeComplete={ this.setSecondaryColor }
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

	setSecondaryColor( color ) {
		this.props.setAttributes( { secondaryColor: color === undefined ? '#0065AE' : color.hex } );
	};
}
export default GlossaryWrapper;