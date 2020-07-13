const BLOCK_CLASS = `wp-block-arteeo-glossary-block`;

/**
 * Render glossary if necessary.
 */
import { render } from '@wordpress/element';
import Glossary from './glossary';

window.addEventListener( 'DOMContentLoaded', () => {
	const wrappers = document.getElementsByClassName(
		BLOCK_CLASS
	);

	let props = {};
	props.attributes = {};
	props.name = 'arteeo/glossary-block';

	for ( const wrapper of wrappers ) {
		props.attributes.secondaryColor = wrapper.dataset.secondaryColor;
		props.attributes.primaryColor   = wrapper.dataset.primaryColor;
		props.name                      = wrapper.dataset.name;
		props.locale                    = wrapper.dataset.locale;
		props.__selectLetter            = wrapper.dataset.__selectLetter;
		if ( ! wrapper.classList.contains('edit') ) {
			render( <Glossary {...props}/>, wrapper );
		}
	}
} );

/**
 * Enable auto resizing inside container
 */
let animationFrameID = null;
const containerWidth = [];

function animationFrameLoop() {
	const containers = document.querySelectorAll(
		'.' + BLOCK_CLASS
	);

	containers.forEach( function ( currentValue, currentIndex, listObj ) {
		const newContainerWidth = currentValue.offsetWidth;

		if ( newContainerWidth !== containerWidth[ currentIndex ] ) {
			handleContainerWidthChanged( currentValue, newContainerWidth );
			containerWidth[ currentIndex ] = newContainerWidth;
		}
	}, '' );

	animationFrameID = window.requestAnimationFrame( animationFrameLoop );
}

function handleContainerWidthChanged( container, newContainerWidth ) {
	container.classList.toggle( 'sm', newContainerWidth >= 576 );
	container.classList.toggle( 'md', newContainerWidth >= 768 );
	container.classList.toggle( 'lg', newContainerWidth >= 992 );
	container.classList.toggle( 'xl', newContainerWidth >= 1200 );
}

document.addEventListener( 'DOMContentLoaded', () => {
	animationFrameLoop();
} );
