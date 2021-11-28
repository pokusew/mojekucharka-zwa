"use strict";

import webpack from 'webpack';
import { merge } from 'webpack-merge';
import path from 'path';
import baseConfig, { srcDir } from './webpack.config.base';


const port = 3000;
const publicPath = `http://localhost:${port}/`;

export default merge(baseConfig, {

	mode: 'development',

	// see https://webpack.js.org/configuration/devtool/#devtool
	devtool: 'eval-source-map',

	entry: {
		index: [
			// injected automatically by the webpack-dev-server (see devServer.hot/client)
			// `webpack-dev-server/client?http://localhost:${port}/`,
			// 'webpack/hot/only-dev-server',
			path.join(__dirname, 'frontend/index'),
		],
	},

	output: {
		publicPath,
	},

	module: {
		rules: [
			{
				test: /\.css?$/,
				use: [
					'style-loader',
					'css-loader',
				],
				include: [
					srcDir,
				],
			},
			{
				test: /\.scss$/,
				use: [
					'style-loader',
					'css-loader',
					// https://github.com/bholloway/resolve-url-loader/blob/v5/packages/resolve-url-loader/README.md
					'resolve-url-loader',
					// PostCSS options are automatically loaded from postcss.config.js
					'postcss-loader',
					'sass-loader',
				],
				include: [
					srcDir,
				],
			},
		],
	},

	plugins: [

		new webpack.LoaderOptionsPlugin({
			debug: true,
		}),

		// automatically injected by the webpack-dev-server (when devServer.hot is true or 'only'))
		// new webpack.HotModuleReplacementPlugin(),

		new webpack.DefinePlugin({
			__DEV__: true,
			// note: it seems that to get rid out of the process/browser.js shim
			//       'process': false is also required
			//       maybe it is related to discussion in https://github.com/webpack/webpack/issues/798
			'process': false,
			'process.env.NODE_ENV': JSON.stringify('development'),
		}),

		// https://github.com/jantimon/html-webpack-plugin
		// new HtmlWebpackPlugin({
		// 	filename: 'index.html',
		// 	template: './src/template.ejs',
		// 	templateParameters,
		// 	chunks: ['index'],
		// 	xhtml: true,
		// }),

	],

	optimization: {
		emitOnErrors: false,
	},

	devServer: {

		// currently, we use webpack-dev-server v4.x
		// see the docs at https://webpack.js.org/configuration/dev-server/

		// host: '0.0.0.0',
		port,

		hot: 'only',
		client: {
			// see https://webpack.js.org/configuration/dev-server/#devserverclient
			overlay: {
				errors: true,
				warnings: false,
			},
			webSocketURL: `auto://0.0.0.0:${port}/ws`,
		},

		magicHtml: false,

		// historyApiFallback: true,
		// static: [
		// 	{
		// 		directory: path.join(__dirname, 'data'),
		// 		publicPath: '/data/',
		// 		// https://github.com/expressjs/serve-index
		// 		serveIndex: false,
		// 		watch: false,
		// 	},
		// ],
		historyApiFallback: false,
		static: false,

		headers: {
			'Access-Control-Allow-Origin': '*',
		},

		// see https://webpack.js.org/configuration/dev-server/#devserverdevmiddleware
		devMiddleware: {
			// see https://github.com/webpack/webpack-dev-middleware#options
			// publicPath, // unnecessary (defaults to output.publicPath)
			writeToDisk: true,
		},

	},

});
