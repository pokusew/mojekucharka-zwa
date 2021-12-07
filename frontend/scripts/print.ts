import './utils';

const handlePrintButtonClick = (event) => {
	window.print();
};

export const registerPrintButtons = () => {

	const buttons = document.getElementsByClassName('btn-print');

	for (const btn of buttons) {
		btn.addEventListener('click', event => handlePrintButtonClick(event));
	}

};
