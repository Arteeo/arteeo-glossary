const path = require('path');
const webpack = require( 'webpack' );
const CopyPlugin = require('copy-webpack-plugin');

module.exports = {
  entry: './src/block/resize.js',
  output: {
    path: path.resolve(__dirname, 'dist'),
    filename: 'js/resize.js'
  },
  module: {
		rules: [
			{
				test: /\.(js|jsx|mjs)$/,
				exclude: /(node_modules|bower_components)/,
				use: {
          loader: 'babel-loader',
          options: {
            presets: ['env']
          }
				},
			},
		],
	},
  plugins: [
    new webpack.optimize.UglifyJsPlugin( {
			compress: {
				warnings: false,
			},
			mangle: {
				safari10: true,
				except: ['__', '_n', '_x', '_nx' ],
			},
			output: {
				comments: false,
				ascii_only: true,
			},
			sourceMap: false,
		}),
    new CopyPlugin([
		{ from: 'src/admin-page/', to: 'php/admin-page/' },
		{ from: 'src/db/', to: 'php/db/' },
		{ from: 'src/api/', to: 'php/api/' },
        { from: 'src/helper/', to: 'php/helper/' },
		{ from: 'src/block.php', to: 'php/block/block.php' },
		{ from: 'src/class-glossary.php', to: 'php/class-glossary.php' },
        { from: 'plugin.php', to: 'plugin.php' },
        { from: 'readme.md', to: 'readme.md' },
        { from: 'dist/blocks.build.js', to: 'js/block/'},
        { from: 'dist/blocks.editor.build.css', to: 'css/block/'},
        { from: 'dist/blocks.style.build.css', to: 'css/block/'},
    ]),
  ],
};