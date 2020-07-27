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

fs.copyFileSync( './WordPress-readme.txt', './dist/readme.txt' );
console.log( 'WordPress-readme.txt was copied to /dist/readme.txt'.green );

fs.mkdirSync( './dist/css/block', { recursive: true } );
fs.copyFileSync(
	'./dist/js/block/glossary-by-arteeo-block-backend.css',
	'./dist/css/block/glossary-by-arteeo.css'
);
console.log(
	'glossary-by-arteeo-block-backend.css was copied to /dist/css/block/glossary-by-arteeo.css'
		.green
);

fs.unlinkSync( 'dist/js/block/glossary-by-arteeo-block-backend.asset.php' );
fs.unlinkSync( 'dist/js/block/glossary-by-arteeo-block-frontend.asset.php' );
fs.unlinkSync( 'dist/js/block/glossary-by-arteeo-block-resize.asset.php' );
fs.unlinkSync( 'dist/js/block/glossary-by-arteeo-block-backend.css' );
fs.unlinkSync( 'dist/js/block/glossary-by-arteeo-block-frontend.css' );
console.log( 'Removed unused assets and files'.green );
