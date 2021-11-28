"use strict";

import path from 'path';
import webpack from 'webpack';
import MiniCssExtractPlugin from 'mini-css-extract-plugin';
import { merge } from 'webpack-merge';
// import { SubresourceIntegrityPlugin } from 'webpack-subresource-integrity';
// import HtmlWebpackPlugin from 'html-webpack-plugin';
// import { templateParameters } from './tools/webpack-utils';
// import AssetsPlugin from 'assets-webpack-plugin';
import WebpackAssetsManifest from 'webpack-assets-manifest';

import baseConfig, { srcDir } from './webpack.config.base';


// [hash] vs [chunkhash] vs [contenthash] > contenthash is best for our use-case
// see https://stackoverflow.com/a/52786672
//     (https://stackoverflow.com/questions/35176489/what-is-the-purpose-of-webpack-hash-and-chunkhash)

export default merge(baseConfig, {

	mode: 'production',

	// see https://webpack.js.org/configuration/devtool/#devtool
	devtool: 'source-map',

	entry: {
		index: [
			path.join(__dirname, 'frontend/index'),
		],
	},

	output: {
		filename: (pathData, assetInfo) => {

			// note: this is currently not needed as it is handled by the InjectManifest plugin
			// Service Worker scripts should always have the same name
			// if (pathData.chunk.name === 'sw') {
			// 	return '[name].js';
			// }

			return '[name].[contenthash].imt.js';

		},
		publicPath: '/',
		// https://github.com/waysact/webpack-subresource-integrity#webpack-configuration-example
		crossOriginLoading: 'anonymous',
	},

	module: {
		rules: [
			{
				test: /\.css?$/,
				use: [
					{
						loader: MiniCssExtractPlugin.loader,
					},
					'css-loader',
				],
				include: [
					srcDir,
				],
			},
			{
				test: /\.s?css$/,
				use: [
					{
						loader: MiniCssExtractPlugin.loader,
					},
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

		new webpack.DefinePlugin({
			// note: it seems that to get rid out of the process/browser.js shim
			//       'process': false is also required
			//       maybe it is related to discussion in https://github.com/webpack/webpack/issues/798
			'process': false,
			'process.env.NODE_ENV': JSON.stringify('production'),
		}),

		// https://github.com/webpack-contrib/mini-css-extract-plugin
		// https://webpack.js.org/plugins/mini-css-extract-plugin/
		new MiniCssExtractPlugin({
			filename: 'style.[contenthash].imt.css',
		}),

		// WebpackAssetsManifest generates integrity internally (via webpack-subresource-integrity)
		// https://github.com/waysact/webpack-subresource-integrity
		// new SubresourceIntegrityPlugin({
		// 	hashFuncNames: ['sha256', 'sha384'],
		// }),

		// https://github.com/jantimon/html-webpack-plugin
		// new HtmlWebpackPlugin({
		// 	filename: 'index.html',
		// 	template: './src/template.ejs',
		// 	templateParameters,
		// 	chunks: ['index'],
		// 	xhtml: true,
		// }),

		// https://github.com/ztoben/assets-webpack-plugin
		// new AssetsPlugin({
		// 	// see https://github.com/ztoben/assets-webpack-plugin#options
		// 	filename: 'assets.json',
		// 	// path: 'dist',
		// 	// integrity: true, // TODO: does not work
		// }),

		// https://github.com/webdeveric/webpack-assets-manifest
		new WebpackAssetsManifest({
			// see https://github.com/webdeveric/webpack-assets-manifest#options-read-the-schema
			output: '../backend/assets.json', // place in the project root
			integrity: true,
			integrityHashes: ['sha256', 'sha384'],
		}),

	],

	optimization: {
		// minimize: true, // (true by default for mode=production)
	},

});
