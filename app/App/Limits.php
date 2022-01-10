<?php

declare(strict_types=1);

namespace App;

/**
 * App-wide limits to prevent duplication.
 */
class Limits
{

	public const
		USERNAME_MIN_LENGTH = 4,
		USERNAME_MAX_LENGTH = 15,
		// see https://stackoverflow.com/questions/386294/what-is-the-maximum-length-of-a-valid-email-address
		EMAIL_MAX_LENGTH = 254,
		PASSWORD_MIN_LENGTH = 8,
		PASSWORD_MAX_LENGTH = 64,
		RECIPE_MIN_LENGTH = 4,
		RECIPE_MAX_LENGTH = 255,
		DEFAULT_CATEGORY = 21,
		PASSWORD_RESET_KEY_EXPIRATION = '1 HOUR'; // must be va lid SQL INTERVAL

}
