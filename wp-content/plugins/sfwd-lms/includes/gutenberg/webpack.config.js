const path = require( 'path' );
//const webpack = require( 'webpack' );
const ExtractTextPlugin = require( 'extract-text-webpack-plugin' );

// Set different CSS extraction for editor only and common block styles
const blocksCSSPlugin = new ExtractTextPlugin( './assets/css/blocks.style.css' );
const editBlocksCSSPlugin = new ExtractTextPlugin( './assets/css/blocks.editor.css' );

module.exports = {
  mode: process.env.NODE_ENV === 'production' ? 'production' : 'development',	
  entry: {
    './assets/js/editor.blocks' : './blocks/index.js',
    './assets/js/frontend.blocks' : './blocks/frontend.js',
  },
  output: {
    path: path.resolve( __dirname ),
    filename: '[name].js',
  },
  devtool: 'cheap-eval-source-map',
  module: {
    rules: [
      {
        test: /\.js$/,
        use: { loader: 'babel-loader' },
        exclude: /(node_modules|bower_components)/,
      },
      {
        test: /style\.s?css$/,
        use: blocksCSSPlugin.extract( { 
			use: ["css-loader","sass-loader"]
		} ),
      },
      {
        test: /editor\.s?css$/,
        use: editBlocksCSSPlugin.extract( {
        	use: ["css-loader","sass-loader"]
        } ),
      },
    ],
  },
  plugins: [
    blocksCSSPlugin,
    editBlocksCSSPlugin,
  ],
};
