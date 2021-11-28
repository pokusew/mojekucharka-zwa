"use strict";

// see https://webpack.js.org/api/loaders/
// see https://webpack.js.org/contribute/writing-a-loader/
module.exports = function (source) {

	const callback = this.async();

	const manifest = JSON.parse(source);

	const work = manifest.icons
		? manifest.icons.map(icon => {

			return new Promise(((resolve, reject) => {

				this.loadModule(icon.src, (err, source, sourceMap, module) => {

					// TODO: find out correct way
					const path = '/' + Object.keys(module.buildInfo.assets)[0];

					if (err) {
						reject(err);
						return;
					}

					icon.src = path;

					resolve();

				});

			}));

		})
		: [];

	Promise.all(work)
		.then(() => {
			callback(null, JSON.stringify(manifest) + '\n');
		})
		.catch(err => {
			callback(err, JSON.stringify(manifest) + '\n');
		});

};
