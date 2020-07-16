class Glossary{
	constructor(container, primaryColor, secondaryColor, __selectLetter, locale) {
		this.primaryColor  = primaryColor;
		this.secondaryColor = secondaryColor;
		this.__selectLetter = __selectLetter;
		this.locale = locale;
		this.container = container;
		this.selectedLetter = '';

		this.wrapper = document.createElement('div');
		this.wrapper.classList.add('wrapper');
		this.container.appendChild(this.wrapper);

		this.init();
	}

	async init() {
		let letters = await this.getLetters();

		this.letters = letters;
		if( 0 < letters.length ) {
			this.selectedLetter = letters[0];
			let entries = await this.getEntries( this.selectedLetter );
			if( 0 < entries.length ) {
				this.entries = entries;
			} else {
				//ToDo: Redirect?
			}
		} else {
			//ToDo: Return empty.
		}

		this.render();
	}

	async getLetters() {
		let response = await fetch('/index.php?rest_route=/arteeo/glossary/v1/letters&locale=de_DE');
		let letters  = await response.json();

		let count = letters.length;
		letters = letters.filter( letter => ( '#' !== letter ) );
		if( count > letters.length ) {
			letters.push('#');
		}

		return letters;
	}

	async getEntries( letter ) {
		let response = await fetch('/index.php?rest_route=/arteeo/glossary/v1/entries&locale=de_DE&letter=' + letter );
		let entries  = await response.json();
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

	render() {
		this.wrapper.innerHTML = '';
		let glossary = this.getTemplate();
		glossary = glossary.replace('{{selectedLetter}}', this.selectedLetter);
		glossary = glossary.replace('{{__selectLetter}}', this.__selectLetter);
		glossary = glossary.replace('{{renderedLetters}}', this.renderLetters());
		glossary = glossary.replace('{{renderedEntries}}', this.renderEntries());

		this.wrapper.innerHTML = glossary;
	}

	getTemplate() {
		return '' +
			'	<section class="sidebar">' + '\n' +
			'		<div class="sidebar-header">' + '\n' +
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
						'color: ' + this.secondaryColor  + '; ' + 
						'borderColor: ' + this.secondaryColor + ';">' + 
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
			'				<h2 style="color: ' + this.secondaryColor + ';">{{term}}</h2>' + '\n' +
			'			</div>' + '\n' +
			'			<div class="description">' + '\n' +
			'				<p>{{description}}</p>' + '\n' +
			'			</div>' + '\n' +
			'		</article>' + '\n';
	}
}

export default Glossary;