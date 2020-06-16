const path = require('path');
const webpack = require( 'webpack' );
const CopyPlugin = require('copy-webpack-plugin');

module.exports = {
  entry: './src/block/resize.js',
  output: {
    path: path.resolve(__dirname, 'dist'),
    filename: 'resize.js'
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
        { from: 'src/admin-page/', to: 'admin-page/' },
        { from: 'src/db/', to: 'db/' },
        { from: 'src/helper/', to: 'helper/' },
        { from: 'src/block.php', to: 'block.php' },
    ]),
  ],
};