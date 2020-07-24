/**
 * Frontend wrapper
 *
 * Loads the glossary when active in the frontend
 *
 * @since 1.0.0
 */
/*global arteeoGlossaryGlobal */
import '../../stylesheets/main.scss';
import Glossary from './class-glossary';
const BLOCK_CLASS = `wp-block-glossary-by-arteeo-frontend`;

/**
 * Event listener
 *
 * Searches the site for instances of the glossary block and renders the block if necessary.
 *
 * @since 1.0.0
 */
document.addEventListener( 'DOMContentLoaded', () => {
	const wrappers = document.getElementsByClassName( BLOCK_CLASS );

	for ( const wrapper of wrappers ) {
		const colors = {};
		colors.primary = wrapper.dataset.primaryColor;
		colors.accent = wrapper.dataset.accentColor;

		if ( ! wrapper.classList.contains( 'edit' ) ) {
			new Glossary(
				wrapper,
				colors,
				arteeoGlossaryGlobal.translations,
				arteeoGlossaryGlobal.locale
			);
		}
	}
} );
