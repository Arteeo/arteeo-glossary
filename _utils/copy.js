/* eslint-disable no-console */
const fs = require( 'fs' );
const copydir = require( 'copy-dir' );
// eslint-disable-next-line no-unused-vars
const colors = require( 'colors' );

copydir.sync( './src/php', './dist/php', {
	utimes: true,
	mode: true,
	cover: true,
} );
console.log( 'Copied php folder'.green );

fs.copyFileSync( './plugin.php', './dist/plugin.php' );
console.log( 'plugin.php was copied to /dist/plugin.php'.green );

fs.copyFileSync( './readme.md', './dist/readme.md' );
console.log( 'readme.md was copied to /dist/readme.md'.green );

fs.mkdirSync( './dist/css/block', { recursive: true } );
fs.copyFileSync(
	'./dist/js/block/arteeo-glossary-block-backend.css',
	'./dist/css/block/arteeo-glossary.css'
);
console.log(
	'arteeo-glossary-block-backend.css was copied to /dist/css/block/arteeo-glossary.css'
		.green
);

fs.unlinkSync( 'dist/js/block/arteeo-glossary-block-backend.asset.php' );
fs.unlinkSync( 'dist/js/block/arteeo-glossary-block-frontend.asset.php' );
fs.unlinkSync( 'dist/js/block/arteeo-glossary-block-resize.asset.php' );
fs.unlinkSync( 'dist/js/block/arteeo-glossary-block-backend.css' );
fs.unlinkSync( 'dist/js/block/arteeo-glossary-block-frontend.css' );
console.log( 'Removed unused assets and files'.green );
