"use strict";

import './utils';


const handlePrintButtonClick = (event) => {
	window.print();
};

export const registerPrintButtons = () => {
	const buttons = document.getElementsByClassName('btn-print');
	for (const btn of buttons) {
		btn.addEventListener('click', handlePrintButtonClick);
	}
};

export const unregisterPrintButtons = () => {
	const buttons = document.getElementsByClassName('btn-print');
	for (const btn of buttons) {
		btn.removeEventListener('click', handlePrintButtonClick);
	}
};
