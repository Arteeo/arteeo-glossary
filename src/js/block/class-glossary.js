import apiFetch from '@wordpress/api-fetch';

/**
 * Glossary
 *
 * Renders and manages the glossary. Gets the letters and entries with fetch calls from the api.
 *
 * @since 1.0.0
 */
class Glossary {
	/**
	 * Constructor
	 *
	 * Constructs and renders the glossary component. Also sets all necessary class attributes.
	 *
	 * @since 1.0.0
	 * @constructs
	 * @param {Element} container the container in which the Glossary will be injected.
	 * @param {Object} colors An object with the primary and accent color.
	 * @param {Object} translations An object with the translations needed by this class.
	 * @param {string} locale The current locale set for this site by php.
	 */
	constructor( container, colors, translations, locale ) {
		this.container = container;
		this.colors = colors;
		this.translations = translations;
		this.locale = locale;
		this.selectedLetter = '';

		this.wrapper = document.createElement( 'div' );
		this.wrapper.classList.add( 'wrapper' );
		this.container.appendChild( this.wrapper );

		this.init();
	}

	/**
	 * Initialiser
	 *
	 * Initialiess the glossary with the first letter available for the locale.
	 *
	 * @since 1.0.0
	 */
	async init() {
		try {
			const letters = await this.getLetters();
			this.letters = letters;
			this.selectedLetter = letters[ 0 ];
			const entries = await this.getEntries();
			this.entries = entries;
			this.render();
		} catch ( error ) {
			this.renderMessage( error.message );
		}
	}

	/**
	 * Get letters
	 *
	 * Requests the letters for the current locale from the api and returns them.
	 *
	 * @since 1.0.0
	 * @throws {Error} If the fetch call gets a wrong response.status or no letters are returned by the api.
	 * @return {Array} The letters provided by the api.
	 */
	async getLetters() {
		const response = await apiFetch( {
			path: '/arteeo/glossary/v1/letters?locale=' + this.locale,
			parse: false,
		} );

		if ( response.status >= 400 && response.status < 600 ) {
			throw new Error( this.translations.apiError );
		}

		let letters = await response.json();
		if ( letters === undefined || letters.length === 0 ) {
			throw new Error( this.translations.noEntry );
		}

		const count = letters.length;
		letters = letters.filter( ( letter ) => '#' !== letter );
		if ( count > letters.length ) {
			letters.push( '#' );
		}

		return letters;
	}

	/**
	 * Get entries
	 *
	 * Requests the entries for the current locale from the api and returns them.
	 *
	 * @since 1.0.0
	 * @throws {Error} If the fetch call gets a wrong response.status or no entries are returned by the api.
	 * @return {Array} The entries provided by the api.
	 */
	async getEntries() {
		const response = await apiFetch( {
			path:
				'/arteeo/glossary/v1/entries?locale=' +
				this.locale +
				'&letter=' +
				this.selectedLetter,
			parse: false,
		} );

		if ( response.status >= 400 && response.status < 600 ) {
			throw new Error( this.translations.apiError );
		}

		const entries = await response.json();
		if ( entries === undefined || entries.length === 0 ) {
			throw new Error( this.translations.apiError );
		}

		return entries;
	}

	/**
	 * Render letters
	 *
	 * Generates the html code for the letters currently loaded by the glossary.
	 *
	 * @since 1.0.0
	 * @return {string} The html-code for the letters.
	 */
	renderLetters() {
		let result = '';
		this.letters.forEach( ( letter ) => {
			let letterTemplate = this.getLetterTemplate(
				letter === this.selectedLetter
			);
			letterTemplate = letterTemplate.replace( /{{letter}}/g, letter );

			result += '				';
			result += letterTemplate;
		} );
		return result;
	}

	/**
	 * Render entries
	 *
	 * Generates the html code for the entries currently loaded by the glossary.
	 *
	 * @since 1.0.0
	 * @return {string} The html-code for the entries.
	 */
	renderEntries() {
		let result = '';
		this.entries.forEach( ( entry ) => {
			let entryTemplate = this.getEntryTemplate();
			entryTemplate = entryTemplate.replace( /{{term}}/g, entry.term );
			entryTemplate = entryTemplate.replace(
				/{{description}}/g,
				entry.description
			);

			result += entryTemplate;
		} );
		return result;
	}

	/**
	 * Render message
	 *
	 * Renders the message provided inside the glossary. This is primarily used to show errors.
	 *
	 * @since 1.0.0
	 * @param {string} message The message to be rendered.
	 */
	renderMessage( message ) {
		this.selectedLetter = '?';
		this.letters = [];
		this.letters.push( '?' );

		this.entries = [];
		const emptyEntry = {};
		emptyEntry.term = '?';
		emptyEntry.description = message;
		this.entries.push( emptyEntry );

		this.render();
	}

	/**
	 * Render glossary
	 *
	 * Generates and injects the html-code for the glossary into the wrapper object.
	 *
	 * @since 1.0.0
	 */
	render() {
		this.wrapper.innerHTML = '';
		let glossary = this.getTemplate();
		glossary = glossary.replace(
			/{{selectedLetter}}/g,
			this.selectedLetter
		);
		glossary = glossary.replace(
			/{{__selectLetter}}/g,
			this.translations.selectLetter
		);
		glossary = glossary.replace(
			/{{renderedLetters}}/g,
			this.renderLetters()
		);
		glossary = glossary.replace(
			/{{renderedEntries}}/g,
			this.renderEntries()
		);

		this.wrapper.innerHTML = glossary;
		this.setEventListeners();
	}

	/**
	 * Get glossary template
	 *
	 * Returns the html-structure of the glossary component.
	 *
	 * @since 1.0.0
	 * @return {string} html-code for glossary component.
	 */
	getTemplate() {
		return (
			/* eslint-disable */
			'	<section class="sidebar">' + '\n' +
			'		<div class="sidebar-header" style="background-color:' + this.colors.primary + ';">' + '\n' +
			'			<div class="letter">' + '\n' +
			'				<h2>{{selectedLetter}}</h2>' + '\n' +
			'			</div>' + '\n' +
			'		</div>' + '\n' +
			'		<div class="sidebar-content">' + '\n' +
			'			<h3>{{__selectLetter}}</h3>' + '\n' +
			'			<div class="letters">'+ '\n' +
							'{{renderedLetters}}' +
			'			</div>'+ '\n' +
			'		</div>' + '\n' +
			'	</section>' + '\n' +
			'	<main class="content">' + '\n' +
					'{{renderedEntries}}' + 
			'	</main>' + '\n'
			/* eslint-enable */
		);
	}

	/**
	 * Get letter template
	 *
	 * Returns the html-structure of a letter within the glossary.
	 *
	 * @since 1.0.0
	 * @param {boolean} active If the letter is currently selected.
	 * @return {string} html-code for glossary letter.
	 */
	getLetterTemplate( active = false ) {
		if ( active ) {
			return (
				/* eslint-disable */
				'<a class="active letter-control" data-letter="{{letter}}" style="' +
						'color: ' + this.colors.accent  + '; ' + 
						'border-color: ' + this.colors.accent + ';">' + 
					'{{letter}}' + 
				'</a>' + '\n'
				/* eslint-enable */
			);
		}

		return (
			/* eslint-disable */
			'<a class="letter-control" data-letter="{{letter}}" >' +
			'	{{letter}}' +
			'</a>' + '\n'
			/* eslint-enable */
		);
	}

	/**
	 * Get enty template
	 *
	 * Returns the html-structure of an entry within the glossary.
	 *
	 * @since 1.0.0
	 * @return {string} html-code for glossary entry.
	 */
	getEntryTemplate() {
		return (
			/* eslint-disable */
			'		<article class="entry">' + '\n' +
			'			<div class="term">' + '\n' +
			'				<h2 style="color: ' + this.colors.accent + ';">{{term}}</h2>' + '\n' +
			'			</div>' + '\n' +
			'			<div class="description">' + '\n' +
			'				<p>{{description}}</p>' + '\n' +
			'			</div>' + '\n' +
			'		</article>' + '\n'
			/* eslint-enable */
		);
	}

	/**
	 * Set colors
	 *
	 * Replaces the colors of the glossary component.
	 *
	 * @since 1.0.0
	 * @param {Object} colors Object which contains the new primary and accent color for the glossary.
	 */
	setColors( colors ) {
		this.colors = colors;
	}

	/**
	 * Set letter listener
	 *
	 * Sets the event listeners on the glossary letters in order for them to be able to change the current letter.
	 *
	 * @since 1.0.0
	 */
	setEventListeners() {
		const letterNodes = this.wrapper.getElementsByClassName(
			'letter-control'
		);

		for ( let i = 0; i < letterNodes.length; i++ ) {
			const content = letterNodes[ i ].dataset.letter;
			letterNodes[ i ].onclick = async () => {
				try {
					this.selectedLetter = content;
					const entries = await this.getEntries();
					this.entries = entries;
					this.render();
				} catch ( error ) {
					this.renderMessage( error.message );
				}
			};
		}
	}
}

export default Glossary;
