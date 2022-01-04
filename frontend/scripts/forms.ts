"use strict";

import { IS_DEVELOPMENT, isDefined } from './utils';
import { shake } from './shake';


type FormControlElement = HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement;

export const isFormControlElement = (el: any): el is FormControlElement =>
	el instanceof HTMLInputElement
	|| el instanceof HTMLSelectElement
	|| el instanceof HTMLTextAreaElement;

export const isTouched = (el: FormControlElement) => el.dataset.touched === 'true';

export const setTouched = (el: FormControlElement) => {
	el.dataset.touched = 'true';
};

export const unsetTouched = (el: FormControlElement) => {
	delete el.dataset.touched;
};

const validateForm = (form: HTMLFormElement): boolean => {

	if (form.checkValidity()) {
		return true;
	}

	for (const el of form.elements) {
		if (isFormControlElement(el)) {
			setTouched(el);
			validateFormControl(el);
		}
	}

	return false;

};

const updateFormControlFeedback = (container: Element, text: string | null) => {

	let feedbackEl = container.querySelector(':scope > .form-control-feedback');

	if (text === null) {
		if (feedbackEl !== null) {
			feedbackEl.remove();
		}
		return;
	}

	if (feedbackEl === null) {
		feedbackEl = document.createElement('p');
		feedbackEl.classList.add('form-control-feedback');
		container.appendChild(feedbackEl);
	}

	feedbackEl.textContent = text;

};

const validateFormControl = (el: FormControlElement) => {

	if (!isTouched(el)) {
		return;
	}

	const container = el.closest('.form-group');

	if (!isDefined(container)) {
		return;
	}

	if (el.validity.valid) {
		container.classList.remove('has-error');
		updateFormControlFeedback(container, null);
	} else {
		container.classList.add('has-error');
		updateFormControlFeedback(container, el.validationMessage);
	}

};

const handleFormControlEvent = (event: Event) => {

	// use event.target so that this handler can be added
	// on parent elements which receive the event thanks to bubbling
	const el = event.target;

	if (!isFormControlElement(el)) {
		return;
	}

	if (event.type === 'blur') {
		setTouched(el);
	}

	validateFormControl(el);

};

/**
 * Tries to focus the first form control with an error
 *
 * If the focus is already on any form control with an error (even if it is not the first in the form),
 * nothing is done.
 *
 * @param form
 * @return `true` iff the focus was set or had been already set on a form control (from the given form) with an error,
 *         `false` otherwise
 */
const focusFormControlWithError = (form: HTMLFormElement): boolean => {

	const focusedEl = document.querySelector(':focus:invalid');

	// the focus is already on a form control (from the given form) with an error
	if (isFormControlElement(focusedEl) && focusedEl.form === form) {
		shake(focusedEl);
		return true;
	}

	// try to focus the first form control with an error
	const invalidEl = form.querySelector(':scope :invalid');
	if (isFormControlElement(invalidEl)) {
		invalidEl.focus();
		shake(invalidEl);
		return true;
	}

	return false;

};

const handleSubmit = (event: SubmitEvent) => {

	IS_DEVELOPMENT && console.log('[forms] handleSubmit');

	const form = event.currentTarget as HTMLFormElement;

	if (!validateForm(form)) {
		// prevent the form submission
		event.preventDefault();
		focusFormControlWithError(form);
		return;
	}

	// if the form is valid, let the browser send it using its default algorithm

};

const registerForm = (form: HTMLFormElement) => {

	// disable browser's automatic validation on submission, so we can handle it ourselves and show custom UI
	// note that this doesn't disable Constraint Validation API, so we can still make use
	// of the browser's built-in validation rules (but show the errors in our UI)
	form.noValidate = true;

	form.addEventListener('submit', handleSubmit);

	// setup handlers for the interactive validation
	for (const el of form.elements) {
		if (isFormControlElement(el)) {
			el.addEventListener('input', handleFormControlEvent);
			el.addEventListener('blur', handleFormControlEvent);
		}
	}

};

/**
 * Allow submitting forms without validation in development mode when Alt key is pressed
 */
const DEVELOPMENT_ONLY_handleSubmitButtonClick = (event: MouseEvent) => {

	if (!IS_DEVELOPMENT) {
		return;
	}

	if (!event.altKey) {
		return;
	}

	const button = event.currentTarget;

	if (!(button instanceof HTMLButtonElement) || !isDefined(button.form)) {
		return;
	}

	console.log('handleSubmitButtonClick');

	// ignores any submit handlers
	// that are triggered after `button.form.submit()` is called
	// but before the form's submission causes navigation
	event.preventDefault();

	// submit the associated form
	// but ignores any submit handlers and constraints validation
	// useful for development when testing server-side validation
	button.form.submit();

};

const unregisterForm = (form: HTMLFormElement) => {
	form.noValidate = false;
	form.removeEventListener('submit', handleSubmit);
	for (const el of form.elements) {
		if (isFormControlElement(el)) {
			el.removeEventListener('input', handleFormControlEvent);
			el.removeEventListener('blur', handleFormControlEvent);
		}
	}
};

export const registerForms = () => {
	const forms: NodeListOf<HTMLFormElement> = document.querySelectorAll('form[data-validation="true"]');
	for (const form of forms) {
		registerForm(form);
	}
	if (IS_DEVELOPMENT) {
		const submitButtons = document.querySelectorAll('button[type="submit"]');
		for (const button of submitButtons) {
			button.addEventListener('click', DEVELOPMENT_ONLY_handleSubmitButtonClick);
		}
	}
};

export const unregisterForms = () => {
	const forms: NodeListOf<HTMLFormElement> = document.querySelectorAll('form[data-validation="true"]');
	for (const form of forms) {
		unregisterForm(form);
	}
	if (IS_DEVELOPMENT) {
		const submitButtons = document.querySelectorAll('button[type="submit"]');
		for (const button of submitButtons) {
			button.removeEventListener('click', DEVELOPMENT_ONLY_handleSubmitButtonClick);
		}
	}
};
