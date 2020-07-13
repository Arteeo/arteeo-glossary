const fs = require('fs');
const copydir = require('copy-dir');
const colors = require('colors');
 
copydir.sync('./src/php', './dist/php', {
  utimes: true,  // keep add time and modify time
  mode: true,    // keep file mode
  cover: true    // cover file when exists, default is true
});
console.log('Copied php folder'.green);

fs.copyFileSync('./plugin.php', './dist/plugin.php');
console.log('plugin.php was copied to /dist/plugin.php'.green);

fs.copyFileSync('./readme.md', './dist/readme.md');
console.log('readme.md was copied to /dist/readme.md'.green);

fs.mkdirSync('./dist/css/block', { recursive: true });
fs.copyFileSync('./dist/js/block/style-block.css', './dist/css/block/style-block.css');
console.log('style-block.css was copied to /dist/css/block/style-block.css'.green);

fs.unlinkSync('dist/js/block/arteeo-glossary.asset.php');
fs.unlinkSync('dist/js/block/block.asset.php');
fs.unlinkSync('dist/js/block/style-block.css');
console.log('Removed unused assets and files'.green);
  