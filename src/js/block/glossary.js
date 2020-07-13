import { __ } from '@wordpress/i18n'; // Import __() from wp.i18n
import { InspectorControls } from '@wordpress/blockEditor';
import { PanelBody, ColorPicker, Spinner } from '@wordpress/components';
import { Component } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

class Glossary extends Component {
	constructor( props ) {
		super( props );
		console.log(this.props.name, ": constructor()");

		// example how to bind `this` to the current component for our callbacks
		this.onChangeContent = this.onChangeContent.bind(this);

		// some place for your state
		this.state = {
			letters: [],
			entries: [],
			loading: false,
			renderedEntries: [],
			renderedLetters: [],
			activeLetter: '',
			editable: ( 'true' === this.props.editable )
		};
	}

	renderLetters( letters, active ) {
		console.log(active);
		let result = [];
		letters.forEach( ( letter ) => {
			if( letter === active ) {
				result.push(<a key={letter} className="active" style={{ color: this.props.attributes.secondaryColor, borderColor: this.props.attributes.secondaryColor}}>{ letter }</a>);
			} else {
				result.push(<a key={letter} onClick={ () => this.updateEntries( letter ) } >{ letter }</a>)
			}
		});

		this.setState({
			renderedLetters: result,
			activeLetter: active,
		});
	}
	
	renderEntries( entries ) {
		let result = [];

		entries.forEach( (entry) => {
			result.push(
				<article className="entry" key={entry.term}>
					<div className="term">
						<h2 style={{ color: this.props.attributes.secondaryColor}}>{entry.term}</h2>
					</div>
					<div className="description">
						<p>{entry.description}</p>
					</div>
				</article>
			);
		});
		
		this.setState({ renderedEntries: result });
	}

	renderInspectorControls() {
		if( this.state.editable ) {
			return (
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
			);
		} else {
			return;
		}
	}

	async updateEntries( letter ) {
		let entries = await this.getEntries( letter );
		this.renderEntries(entries);

		this.renderLetters( this.state.letters, letter );
	}

	async getEntries( letter ) {
		console.log('Get Entries begun.')

		this.setState( { loading: true } );

		let entries = await apiFetch( { path: '/arteeo/glossary/v1/entries?locale=' + this.props.locale + '&letter=' + letter } );

		this.setState( { 
			loading: false,
			entries: entries,
		} );

		return entries;
	}

	async getLetters() {
		console.log(1.1);
		this.setState( { loading: true } );
		console.log(1.2);
		let letters = await apiFetch( { path: '/arteeo/glossary/v1/letters?locale=' + this.props.locale } );

		console.log(1.3);
		this.setState( { 
			loading: false,
			letters: letters,
		} );

		console.log(1.4);
		return letters;
	}

	setPrimaryColor( color ) {
		this.props.setAttributes( { primaryColor: color === undefined ? '#0065AE' : color.hex } );
	};

	setSecondaryColor( color ) {
		this.props.setAttributes( { secondaryColor: color === undefined ? '#0065AE' : color.hex } );
	};

	async componentDidMount() {
		console.log(1);
		let letters = await this.getLetters();
		console.log(2);
		this.renderLetters( letters, letters[0] );
		console.log(3);
		await this.updateEntries( letters[0] );
		console.log(this.props.name, ": componentDidMount()");
	}

	componentDidUpdate() {
		console.log(this.props.name, ": componentDidUpdate()");
	}

	componentWillUnmount() {
		console.log(this.props.name, ": componentWillUnmount()");
	}

	// update attributes when content is updated
	onChangeContent(data) {
		// set attribute the react way
		this.props.setAttributes({ content: data });
	}

	// edit: function (props) {
	render() {
		if( this.state.loading ) {
			return <Spinner />;
		}
		return (
			<div className="wrapper"> 
				{ this.renderInspectorControls() }
				<section className="sidebar">
					<div className="sidebar-header" style={{ backgroundColor: this.props.attributes.primaryColor}}>
						<div className="letter">
							<h2>{ this.state.activeLetter }</h2>
						</div>
					</div>
					<div className="sidebar-content">
						<h3 style={{ color: this.props.attributes.secondaryColor}} >{this.props.__selectLetter}</h3>
						<div className="letters">
							{ this.state.renderedLetters }
						</div>
					</div>
				</section>
				<main className="content">
					{ this.state.renderedEntries }
				</main>
			</div>
		);
	}
}

export default Glossary;