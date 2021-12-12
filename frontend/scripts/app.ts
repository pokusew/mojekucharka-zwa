"use strict";

import { registerPrintButtons, unregisterPrintButtons } from './print';
import { registerForms, unregisterForms } from './forms';


const run = () => {
	registerPrintButtons();
	registerForms();
};

const cleanup = () => {
	unregisterPrintButtons();
	unregisterForms();
};

if (document.readyState === 'loading') {
	// loading hasn't finished yet
	document.addEventListener('DOMContentLoaded', run);
} else {
	// DOMContentLoaded has already fired
	run();
}

// webpack Hot Module Replacement API
// see https://webpack.js.org/api/hot-module-replacement/
// @ts-ignore
if (import.meta.webpackHot) {

	// add a handler which is executed when the current module code is replaced
	// @ts-ignore
	import.meta.webpackHot.dispose((data) => {
		// console.log('dispose in app.ts', data);
		cleanup();
	});

	// accept updates for itself
	// @ts-ignore
	import.meta.webpackHot.accept();

}
