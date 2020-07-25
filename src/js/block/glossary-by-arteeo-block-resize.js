/**
 * Resize handler
 *
 * Controls the size of glossary-blocks on a site.
 *
 * @since 1.0.0
 */
const BLOCK_CLASS = `wp-block-glossary-by-arteeo-frontend`;
const containerWidth = [];

/**
 * Size Loop
 *
 * Checks in an interval if the size of any glossary block has changes and adjusts affected blocks if needed.
 *
 * @since 1.0.0
 */
function animationFrameLoop() {
	const containers = document.querySelectorAll( '.' + BLOCK_CLASS );

	containers.forEach( function ( currentValue, currentIndex ) {
		const newContainerWidth = currentValue.offsetWidth;

		if ( newContainerWidth !== containerWidth[ currentIndex ] ) {
			handleContainerWidthChanged( currentValue, newContainerWidth );
			containerWidth[ currentIndex ] = newContainerWidth;
		}
	}, '' );

	window.requestAnimationFrame( animationFrameLoop );
}

/**
 * Adjust container width
 *
 * Sets the approptiate classes on the container for the site provided.
 *
 * @since 1.0.0
 * @param {Element} container The container which has to be adjusted.
 * @param {number} newContainerWidth The size of the container
 */
function handleContainerWidthChanged( container, newContainerWidth ) {
	container.classList.toggle( 'sm', newContainerWidth >= 576 );
	container.classList.toggle( 'md', newContainerWidth >= 768 );
	container.classList.toggle( 'lg', newContainerWidth >= 992 );
	container.classList.toggle( 'xl', newContainerWidth >= 1200 );
}

/**
 * Event Listener
 *
 * Registers the Size-Loop within javascript.
 *
 * @since 1.0.0
 */
document.addEventListener( 'DOMContentLoaded', () => {
	animationFrameLoop();
} );
