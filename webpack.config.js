const path = require('path');
const CopyPlugin = require('copy-webpack-plugin');

module.exports = {
  entry: './src/block/resize.js',
  output: {
    path: path.resolve(__dirname, 'dist'),
    filename: 'resize.js'
  },
  plugins: [
    new CopyPlugin({
      patterns: [
        { from: 'src/admin-page/', to: 'admin-page/' },
        { from: 'src/db/', to: 'db/' },
        { from: 'src/helper/', to: 'helper/' },
        { from: 'src/block.php', to: 'block.php' },
      ],
    }),
  ],
};