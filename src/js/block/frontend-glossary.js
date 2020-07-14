class Glossary{
	constructor(container, primaryColor, secondaryColor, __selectLetter, locale) {
		this.primaryColor  = primaryColor;
		this.secondaryColor = secondaryColor;
		this.__selectLetter = __selectLetter;
		this.locale = locale;
		this.container = container;
		this.selectedLetter = '';

		/*this.getLetters()
			.then( letters => {
				this.letters = letters;
				if( 0 < letters.lenght ) {
					this.selectedLetter = letters[0];
				} else {
					//ToDo: Return empty.
				}

				this.renderGlossary();
			}
		);*/
		this.init();
	}

	async init() {
		let letters = await this.getLetters();

		this.letters = letters;
		if( 0 < letters.length ) {
			this.selectedLetter = letters[0];
		} else {
			//ToDo: Return empty.
		}

		this.renderGlossary();
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
	renderLetters( parent ) {
		let result = [];
		this.letters.forEach(letter => {
			let letterLink = document.createElement('a');
			letterLink.innerHTML = letter
			parent.appendChild(letterLink);
		});
	}
	renderGlossary() {
		let wrapper = document.createElement('div');
		wrapper.classList.add('wrapper');
		let template = this.getTemplate();
		template = template.replace('{{selectedLetter}}', this.selectedLetter);

		wrapper.innerHTML = template;
		this.container.appendChild(wrapper);
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
			'				{{renderedLetters}}'+ '\n' +
			'			</div>'+ '\n' +
			'		</div>' + '\n' +
			'	</section>' + '\n' +
			'	<main class="content">'+ '\n' +
			'		{{renderedEntries}}'+ '\n' +
			'	</main>'+ '\n';
	}
}

export default Glossary;