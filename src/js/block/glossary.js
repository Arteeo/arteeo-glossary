const { __ } = wp.i18n; // Import __() from wp.i18n
const { InspectorControls } = wp.blockEditor;
const { PanelBody, ColorPicker, Spinner } = wp.components;
const { Component } = wp.element;

const { withSelect } = wp.data;
const { apiFetch } = wp;

class Glossary extends Component {
	constructor( props ) {
		super( props );
		console.log(this.props.name, ": constructor()");

		// example how to bind `this` to the current component for our callbacks
		this.onChangeContent = this.onChangeContent.bind(this);

		this.editable = this.props.editable;

		// some place for your state
		this.state = {
			letters: [],
			entries: [],
			loading: false,
			renderedEntries: [],
			renderedLetters: [],
			activeLetter: '',
		};
	}

	renderLetters( letters, active ) {
		console.log(active);
		let result = [];

		letters.forEach( ( letter ) => {
			if( letter === active ) {
				result.push(<a class="active" style={{ color: this.props.attributes.secondaryColor, borderColor: this.props.attributes.secondaryColor}}>{ letter }</a>);
			} else {
				result.push(<a onClick={ () => this.updateEntries( letter ) } >{ letter }</a>)
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
				<article class="entry">
					<div class="term">
						<h2 style={{ color: this.props.attributes.secondaryColor}}>{entry.term}</h2>
					</div>
					<div class="description">
						<p>{entry.description}</p>
					</div>
				</article>
			);
		});
		
		this.setState({ renderedEntries: result });
	}

	renderInspectorControls() {
		if( this.editable ) {
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

		let entries = await apiFetch( { path: '/arteeo/glossary/v1/entries?locale=' + cgbGlobal.locale + '&letter=' + letter } );

		this.setState( { 
			loading: false,
			entries: entries,
		} );

		return entries;
	}

	async getLetters() {
		this.setState( { loading: true } );

		let letters = await apiFetch( { path: '/arteeo/glossary/v1/letters?locale=' + cgbGlobal.locale } );

		this.setState( { 
			loading: false,
			letters: letters,
		} );

		return letters;
	}

	setPrimaryColor( color ) {
		this.props.setAttributes( { primaryColor: color === undefined ? '#0065AE' : color.hex } );
	};

	setSecondaryColor( color ) {
		this.props.setAttributes( { secondaryColor: color === undefined ? '#0065AE' : color.hex } );
	};

	async componentDidMount() {
		let letters = await this.getLetters();
		this.renderLetters( letters, letters[0] );
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
		} else {
			return (
				<div class="wrapper"> 
					{ this.renderInspectorControls() }
					<section class="sidebar">
						<div class="sidebar-header" style={{ backgroundColor: this.props.attributes.primaryColor}}>
						<div class="letter">
							<h2>{ this.state.activeLetter }</h2>
						</div>
						</div>
						<div class="sidebar-content">
							<h3 style={{ color: this.props.attributes.secondaryColor}} >{cgbGlobal.__selectLetter}</h3>
							<div class="letters">
								{ this.state.renderedLetters }
							</div>
						</div>
					</section>
					<main class="content">
						{ this.state.renderedEntries }
					</main>
				</div>
			);
		}
	}
}

export default Glossary;