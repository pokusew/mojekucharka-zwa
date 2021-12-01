"use strict";

import webpack from 'webpack';
import path from 'path';


export const srcDir = path.resolve(__dirname, 'frontend');

export default {

	module: {
		rules: [
			{
				test: /robots\.txt$/,
				loader: 'file-loader',
				options: {
					name: '[name].[ext]',
				},
				include: [
					srcDir,
				],
			},
			{
				test: /manifest.json$/,
				type: 'javascript/auto',
				use: [
					{
						loader: 'file-loader',
						options: {
							name: '[name].[contenthash].imt.[ext]',
						},
					},
					'web-app-manifest-loader',
				],
				include: [
					srcDir,
				],
			},
			// {
			// 	test: /\.mjs$/,
			// 	type: 'javascript/auto',
			// },
			{
				test: /\.(ts|js)x?$/,
				loader: 'babel-loader',
				options: {
					cacheDirectory: true,
				},
				include: [
					srcDir,
				],
			},
			{
				test: /[^]\.(png|jpg|svg|mp3)$/,
				loader: 'file-loader',
				options: {
					name: '[name].[contenthash].imt.[ext]',
				},
				include: [
					srcDir,
				],
			},
		],
	},

	// https://github.com/webpack/webpack/issues/11660
	target: 'web', // default is browserslist

	resolve: {
		extensions: ['.ts', '.tsx', '.js'],
	},

	resolveLoader: {
		modules: [
			'node_modules',
			path.resolve(__dirname, 'tools'),
		],
	},

	output: {
		path: path.join(__dirname, 'build'),
		filename: '[name].js',
	},

	plugins: [],

	optimization: {
		moduleIds: 'named',
		// minimize: false, // (true by default for production) https://github.com/babel/minify probably does not work (outputs are even bigger)
	},

};
