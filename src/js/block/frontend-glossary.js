import apiFetch from '@wordpress/api-fetch';

class Glossary{
	constructor(container, colors, translations, locale) {
		this.container      = container;
		this.colors         = colors;
		this.translations   = translations;
		this.locale         = locale;
		this.selectedLetter = '';

		this.wrapper = document.createElement('div');
		this.wrapper.classList.add('wrapper');
		this.container.appendChild(this.wrapper);

		this.init();
	}

	async init() {
		let letters = await this.getLetters();
		
		if ( letters !== undefined && letters.length != 0 ) {
			this.letters = letters;
			await this.setSelectedLetter( letters[0] );
			this.render();
		} else {
			this.renderMessage( this.translations.noEntry );
		}
	}

	async getLetters() {
		let letters = await apiFetch( { 
			path: '/arteeo/glossary/v1/letters?locale=' + this.locale
		} );

		let count = letters.length;
		letters = letters.filter( letter => ( '#' !== letter ) );
		if( count > letters.length ) {
			letters.push('#');
		}

		return letters;
	}

	async getEntries( letter ) {
		let entries = await apiFetch( { 
			path: '/arteeo/glossary/v1/entries?locale=' + this.locale + '&letter=' + letter 
		} );
		
		return entries;
	}

	renderLetters() {
		let result = '';
		this.letters.forEach(letter => {
			let letterTemplate = this.getLetterTemplate( (letter === this.selectedLetter) );
			letterTemplate = letterTemplate.replace('{{letter}}', letter);

			result += '				';
			result += letterTemplate;
		});
		return result;
	}

	renderEntries() {
		let result = '';
		this.entries.forEach(entry => {
			let entryTemplate = this.getEntryTemplate();
			entryTemplate = entryTemplate.replace('{{term}}', entry.term);
			entryTemplate = entryTemplate.replace('{{description}}', entry.description);

			result += entryTemplate;
		});
		return result;
	}

	renderMessage( message ) {
		this.selectedLetter = '?';
		this.letters        = [];
		this.letters.push('?');

		this.entries           = [];
		let emptyEntry         = {}
		emptyEntry.term        = '?';
		emptyEntry.description = message;
		this.entries.push(emptyEntry);

		this.render();
	}

	render() {
		this.wrapper.innerHTML = '';
		let glossary = this.getTemplate();
		glossary = glossary.replace('{{selectedLetter}}', this.selectedLetter);
		glossary = glossary.replace('{{__selectLetter}}', this.translations.selectLetter);
		glossary = glossary.replace('{{renderedLetters}}', this.renderLetters());
		glossary = glossary.replace('{{renderedEntries}}', this.renderEntries());

		this.wrapper.innerHTML = glossary;

		//ToDo: Better!!!
		let letterNodes = this.wrapper.getElementsByTagName('a');
		for(let i = 0; i < letterNodes.length; i++)
		{
			const content = letterNodes[i].innerHTML;
			letterNodes[i].onclick = async () => { 
				await this.setSelectedLetter( content )
				this.render();
			}
		}
	}

	getTemplate() {
		return '' +
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
			'	</main>' + '\n';
	}

	getLetterTemplate( active = false ) {
		if ( active ) {
			return '' + 
				'<a class="active" style="' + 
						'color: ' + this.colors.accent  + '; ' + 
						'border-color: ' + this.colors.accent + ';">' + 
					'{{letter}}' + 
				'</a>' + '\n';
		} else {
			return '' + 
				'<a>' + 
					'{{letter}}' + 
				'</a>' + '\n';
		}
	}

	getEntryTemplate() {
		return '' +
			'		<article class="entry">' + '\n' +
			'			<div class="term">' + '\n' +
			'				<h2 style="color: ' + this.colors.accent + ';">{{term}}</h2>' + '\n' +
			'			</div>' + '\n' +
			'			<div class="description">' + '\n' +
			'				<p>{{description}}</p>' + '\n' +
			'			</div>' + '\n' +
			'		</article>' + '\n';
	}

	//ToDo: Better!!!
	async setSelectedLetter( letter ) {
		this.selectedLetter = letter;
		let entries         = await this.getEntries( this.selectedLetter );
		if( entries !== undefined && entries.length != 0 ) {
			this.entries = entries;
		} else {
			console.error('No entries returned.')
			this.renderMessage( this.translations.apiError );
		}
	}

	setColors( colors ) {
		this.colors = colors;
	}
}

export default Glossary;