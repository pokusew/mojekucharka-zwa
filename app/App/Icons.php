<?php

declare(strict_types=1);

namespace App;

/**
 * SVG icons.
 *
 * From [Font Awesome 5](https://fontawesome.com/).
 */
class Icons
{

	public const FA_LOCK_DUOTONE = <<<'SVG'
	<svg aria-hidden="true" focusable="false" data-prefix="fad" data-icon="lock-alt" class="svg-inline--fa fa-lock-alt fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M152 225H72v-72C72 69.2 140.2 1 224 1s152 68.2 152 152v72h-80v-72a72 72 0 0 0-144 0z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M400 225H48a48 48 0 0 0-48 48v192a48 48 0 0 0 48 48h352a48 48 0 0 0 48-48V273a48 48 0 0 0-48-48zM264 392a40 40 0 0 1-80 0v-48a40 40 0 0 1 80 0z"></path></g></svg>
	SVG;

	public const FA_USER_CIRCLE_DUOTONE = <<<SVG
	<svg aria-hidden="true" focusable="false" data-prefix="fad" data-icon="user-circle" class="svg-inline--fa fa-user-circle fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 496 512"><g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M248,8C111,8,0,119,0,256S111,504,248,504,496,393,496,256,385,8,248,8Zm0,96a88,88,0,1,1-88,88A88,88,0,0,1,248,104Zm0,344a191.61,191.61,0,0,1-146.5-68.2C120.3,344.4,157.1,320,200,320a24.76,24.76,0,0,1,7.1,1.1,124.67,124.67,0,0,0,81.8,0A24.76,24.76,0,0,1,296,320c42.9,0,79.7,24.4,98.5,59.8A191.61,191.61,0,0,1,248,448Z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M248,280a88,88,0,1,0-88-88A88,88,0,0,0,248,280Zm48,40a24.76,24.76,0,0,0-7.1,1.1,124.67,124.67,0,0,1-81.8,0A24.76,24.76,0,0,0,200,320c-42.9,0-79.7,24.4-98.5,59.8,68.07,80.91,188.84,91.32,269.75,23.25A192,192,0,0,0,394.5,379.8C375.7,344.4,338.9,320,296,320Z"></path></g></svg>
	SVG;

	public const FA_TIMES_REGULAR = <<<SVG
	<svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="times" class="svg-inline--fa fa-times fa-w-10" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M207.6 256l107.72-107.72c6.23-6.23 6.23-16.34 0-22.58l-25.03-25.03c-6.23-6.23-16.34-6.23-22.58 0L160 208.4 52.28 100.68c-6.23-6.23-16.34-6.23-22.58 0L4.68 125.7c-6.23 6.23-6.23 16.34 0 22.58L112.4 256 4.68 363.72c-6.23 6.23-6.23 16.34 0 22.58l25.03 25.03c6.23 6.23 16.34 6.23 22.58 0L160 303.6l107.72 107.72c6.23 6.23 16.34 6.23 22.58 0l25.03-25.03c6.23-6.23 6.23-16.34 0-22.58L207.6 256z"></path></svg>
	SVG;

	public const FA_TAG_SOLID = <<<SVG
	<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="tag" class="svg-inline--fa fa-tag fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M0 252.118V48C0 21.49 21.49 0 48 0h204.118a48 48 0 0 1 33.941 14.059l211.882 211.882c18.745 18.745 18.745 49.137 0 67.882L293.823 497.941c-18.745 18.745-49.137 18.745-67.882 0L14.059 286.059A48 48 0 0 1 0 252.118zM112 64c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48z"></path></svg>
	SVG;

	public const FA_TAG_DUOTONE = <<<SVG
	<svg aria-hidden="true" focusable="false" data-prefix="fad" data-icon="tag" class="svg-inline--fa fa-tag fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M497.94 225.94L286.06 14.06A48 48 0 0 0 252.12 0H48A48 48 0 0 0 0 48v204.12a48 48 0 0 0 14.06 33.94l211.88 211.88a48 48 0 0 0 67.88 0l204.12-204.12a48 48 0 0 0 0-67.88zM112 160a48 48 0 1 1 48-48 48 48 0 0 1-48 48z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d=""></path></g></svg>
	SVG;

	public const FA_CLOCK_DUOTONE = <<<SVG
	<svg aria-hidden="true" focusable="false" data-prefix="fad" data-icon="clock" class="svg-inline--fa fa-clock fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M256,8C119,8,8,119,8,256S119,504,256,504,504,393,504,256,393,8,256,8Zm92.49,313h0l-20,25a16,16,0,0,1-22.49,2.5h0l-67-49.72a40,40,0,0,1-15-31.23V112a16,16,0,0,1,16-16h32a16,16,0,0,1,16,16V256l58,42.5A16,16,0,0,1,348.49,321Z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M348.49,321h0l-20,25a16,16,0,0,1-22.49,2.5h0l-67-49.72a40,40,0,0,1-15-31.23V112a16,16,0,0,1,16-16h32a16,16,0,0,1,16,16V256l58,42.5A16,16,0,0,1,348.49,321Z"></path></g></svg>
	SVG;

	public const FA_GLOBAL_EUROPA_DUOTONE = <<<SVG
	<svg aria-hidden="true" focusable="false" data-prefix="fad" data-icon="globe-europe" class="svg-inline--fa fa-globe-europe fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 496 512"><g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M487.54,320.4H438.9a15.8,15.8,0,0,1-11.4-4.8l-32-32.6a11.92,11.92,0,0,1,.1-16.7l12.5-12.5v-8.7a11.37,11.37,0,0,0-3.3-8l-9.4-9.4a11.37,11.37,0,0,0-8-3.3h-16a11.31,11.31,0,0,1-8-19.3l9.4-9.4a11.37,11.37,0,0,1,8-3.3h32a11.35,11.35,0,0,0,11.3-11.3v-9.4a11.35,11.35,0,0,0-11.3-11.3H376.1a16,16,0,0,0-16,16v4.5a16,16,0,0,1-10.9,15.2l-31.6,10.5a8,8,0,0,0-5.5,7.6v2.2a8,8,0,0,1-8,8h-16a8,8,0,0,1-8-8,8,8,0,0,0-8-8H269a8.14,8.14,0,0,0-7.2,4.4l-9.4,18.7a15.94,15.94,0,0,1-14.3,8.8H216a16,16,0,0,1-16-16V199a16,16,0,0,1,4.7-11.3l20.1-20.1a24.77,24.77,0,0,0,7.2-17.5,8,8,0,0,1,5.5-7.6l40-13.3a11.66,11.66,0,0,0,4.4-2.7l26.8-26.8a11.31,11.31,0,0,0-8-19.3H280l-16,16v8a8,8,0,0,1-8,8H240a8,8,0,0,1-8-8v-20a8.05,8.05,0,0,1,3.2-6.4l82.42-60.08A247.79,247.79,0,0,0,248,8C111,8,0,119,0,256S111,504,248,504a251.57,251.57,0,0,0,32.1-2.06V448.4a16,16,0,0,0-16-16H243.9c-10.8,0-26.7-5.3-35.4-11.8l-22.2-16.7a45.42,45.42,0,0,1-18.2-36.4V343.6a45.46,45.46,0,0,1,22.1-39l42.9-25.7a46.13,46.13,0,0,1,23.4-6.5h31.2a45.62,45.62,0,0,1,29.6,10.9l43.2,37.1h18.3a32,32,0,0,1,22.6,9.4l17.3,17.3.08.08C432,359.06,440,375.62,440,393.37V413A247.11,247.11,0,0,0,487.54,320.4ZM187.4,157.1a11.37,11.37,0,0,1-8,3.3h-16a11.31,11.31,0,0,1-8-19.3l25.4-25.4a11.31,11.31,0,0,1,19.3,8v16a11.37,11.37,0,0,1-3.3,8Z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M187.4,157.1l9.4-9.4a11.37,11.37,0,0,0,3.3-8v-16a11.31,11.31,0,0,0-19.3-8l-25.4,25.4a11.31,11.31,0,0,0,8,19.3h16A11.37,11.37,0,0,0,187.4,157.1ZM418.78,347.18l-.08-.08-17.3-17.3a32,32,0,0,0-22.6-9.4H360.5l-43.2-37.1a45.62,45.62,0,0,0-29.6-10.9H256.5a46.13,46.13,0,0,0-23.4,6.5l-42.9,25.7a45.46,45.46,0,0,0-22.1,39v23.9a45.42,45.42,0,0,0,18.2,36.4l22.2,16.7c8.7,6.5,24.6,11.8,35.4,11.8h20.2a16,16,0,0,1,16,16v53.54A247.57,247.57,0,0,0,440,413V393.37C440,375.62,432,359.06,418.78,347.18ZM317.62,17.92,235.2,78a8.05,8.05,0,0,0-3.2,6.4v20a8,8,0,0,0,8,8h16a8,8,0,0,0,8-8v-8l16-16h20.7a11.31,11.31,0,0,1,8,19.3l-26.8,26.8a11.66,11.66,0,0,1-4.4,2.7l-40,13.3a8,8,0,0,0-5.5,7.6,24.77,24.77,0,0,1-7.2,17.5l-20.1,20.1A16,16,0,0,0,200,199v25.3a16,16,0,0,0,16,16h22.1a15.94,15.94,0,0,0,14.3-8.8l9.4-18.7a8.14,8.14,0,0,1,7.2-4.4h3.1a8,8,0,0,1,8,8,8,8,0,0,0,8,8h16a8,8,0,0,0,8-8v-2.2a8,8,0,0,1,5.5-7.6l31.6-10.5a16,16,0,0,0,10.9-15.2v-4.5a16,16,0,0,1,16-16h36.7a11.35,11.35,0,0,1,11.3,11.3v9.4a11.35,11.35,0,0,1-11.3,11.3h-32a11.37,11.37,0,0,0-8,3.3l-9.4,9.4a11.31,11.31,0,0,0,8,19.3h16a11.37,11.37,0,0,1,8,3.3l9.4,9.4a11.37,11.37,0,0,1,3.3,8v8.7l-12.5,12.5a11.92,11.92,0,0,0-.1,16.7l32,32.6a15.8,15.8,0,0,0,11.4,4.8h48.64A248.29,248.29,0,0,0,496,256C496,143.18,420.71,48,317.62,17.92Z"></path></g></svg>
	SVG;

	public const FA_USER_DUOTONE = <<<SVG
	<svg aria-hidden="true" focusable="false" data-prefix="fad" data-icon="user" class="svg-inline--fa fa-user fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M352 128A128 128 0 1 1 224 0a128 128 0 0 1 128 128z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M313.6 288h-16.7a174.1 174.1 0 0 1-145.8 0h-16.7A134.43 134.43 0 0 0 0 422.4V464a48 48 0 0 0 48 48h352a48 48 0 0 0 48-48v-41.6A134.43 134.43 0 0 0 313.6 288z"></path></g></svg>
	SVG;

	public const FA_SORT_ALPHA_UP_DUOTONE = <<<SVG
	<svg aria-hidden="true" focusable="false" data-prefix="fad" data-icon="sort-alpha-up" class="svg-inline--fa fa-sort-alpha-up fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M416 288H288a16 16 0 0 0-16 16v32a16 16 0 0 0 16 16h56l-61.26 70.45A32 32 0 0 0 272 446.37V464a16 16 0 0 0 16 16h128a16 16 0 0 0 16-16v-32a16 16 0 0 0-16-16h-56l61.26-70.45A32 32 0 0 0 432 321.63V304a16 16 0 0 0-16-16zm31.06-85.38l-59.27-160A16 16 0 0 0 372.72 32h-41.44a16 16 0 0 0-15.07 10.62l-59.27 160A16 16 0 0 0 272 224h24.83a16 16 0 0 0 15.23-11.08l4.42-12.92h71l4.41 12.92A16 16 0 0 0 407.16 224H432a16 16 0 0 0 15.06-21.38zM335.61 144L352 96l16.39 48z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M16 160h48v304a16 16 0 0 0 16 16h32a16 16 0 0 0 16-16V160h48c14.21 0 21.38-17.24 11.31-27.31l-80-96a16 16 0 0 0-22.62 0l-80 96C-5.35 142.74 1.78 160 16 160z"></path></g></svg>
	SVG;

	public const FA_SORT_ALPHA_DOWN_ALT_DUOTONE = <<<SVG
	<svg aria-hidden="true" focusable="false" data-prefix="fad" data-icon="sort-alpha-down-alt" class="svg-inline--fa fa-sort-alpha-down-alt fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M288 224h128a16 16 0 0 0 16-16v-32a16 16 0 0 0-16-16h-56l61.26-70.45A32 32 0 0 0 432 65.63V48a16 16 0 0 0-16-16H288a16 16 0 0 0-16 16v32a16 16 0 0 0 16 16h56l-61.26 70.45A32 32 0 0 0 272 190.37V208a16 16 0 0 0 16 16zm159.06 234.62l-59.27-160A16 16 0 0 0 372.72 288h-41.44a16 16 0 0 0-15.07 10.62l-59.27 160A16 16 0 0 0 272 480h24.83a16 16 0 0 0 15.23-11.08l4.42-12.92h71l4.41 12.92A16 16 0 0 0 407.16 480H432a16 16 0 0 0 15.06-21.38zM335.61 400L352 352l16.39 48z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M176 352h-48V48a16 16 0 0 0-16-16H80a16 16 0 0 0-16 16v304H16c-14.19 0-21.36 17.24-11.29 27.31l80 96a16 16 0 0 0 22.62 0l80-96C197.35 369.26 190.22 352 176 352z"></path></g></svg>
	SVG;

}
