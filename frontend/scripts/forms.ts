"use strict";

import './utils';


const handleSubmit = (event: SubmitEvent) => {
	event.preventDefault();
	console.log('handleSubmit');
};

export const registerForms = () => {

	const forms: NodeListOf<HTMLFormElement> = document.querySelectorAll('form[data-validation="true"]');

	for (const form of forms) {

		// disable browser's automatic validation on submission, so we can handle it ourselves and show custom UI
		// note that this doesn't disable Constraint Validation API, so we can still make use
		// of the browser's built-in validation rules (but show the errors in our UI)
		form.noValidate = true;

		form.addEventListener('submit', handleSubmit);

	}

};

export const unregisterForms = () => {
	const forms: NodeListOf<HTMLFormElement> = document.querySelectorAll('form[data-validation="true"]');
	for (const form of forms) {
		form.noValidate = false;
		form.removeEventListener('submit', handleSubmit);
	}
};
