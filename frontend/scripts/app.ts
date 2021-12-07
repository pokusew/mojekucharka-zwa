import { registerPrintButtons } from './print';

const run = () => {
	registerPrintButtons();
};

if (document.readyState === 'loading') {
	// loading hasn't finished yet
	document.addEventListener('DOMContentLoaded', () => run());
} else {
	// DOMContentLoaded has already fired
	run();
}
