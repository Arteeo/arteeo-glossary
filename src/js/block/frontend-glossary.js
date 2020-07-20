import apiFetch from '@wordpress/api-fetch';

class Glossary {
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

	getTemplate() {
		return (
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
		);
	}

	getLetterTemplate( active = false ) {
		if ( active ) {
			return (
				'<a class="active letter-control" data-letter="{{letter}}" style="' +
						'color: ' + this.colors.accent  + '; ' + 
						'border-color: ' + this.colors.accent + ';">' + 
					'{{letter}}' + 
				'</a>' + '\n'
			);
		}

		return (
			'<a class="letter-control" data-letter="{{letter}}" >' +
			'	{{letter}}' +
			'</a>' + '\n'
		);
	}

	getEntryTemplate() {
		return (
			'		<article class="entry">' + '\n' +
			'			<div class="term">' + '\n' +
			'				<h2 style="color: ' + this.colors.accent + ';">{{term}}</h2>' + '\n' +
			'			</div>' + '\n' +
			'			<div class="description">' + '\n' +
			'				<p>{{description}}</p>' + '\n' +
			'			</div>' + '\n' +
			'		</article>' + '\n'
		);
	}

	setColors( colors ) {
		this.colors = colors;
	}

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
