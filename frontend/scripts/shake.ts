"use strict";

import { isDefined } from './utils';


/**
 * Tries to play the `shake` CSS animation of the given element
 *
 * See the corresponding CSS in styles/_animations.scss.
 *
 * @param el
 */
export const shake = (el: Element) => {

	const animation = el.getAnimations().find(a => a instanceof CSSAnimation && a.animationName === 'shake');

	if (isDefined(animation)) {
		animation.play();
	}

};
