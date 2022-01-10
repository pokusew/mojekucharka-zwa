<?php

declare(strict_types=1);

namespace App\Service;

/**
 * An exception that can occur during {@see UsersService::changeUserPassword()}.
 */
class UserChangePasswordException extends \RuntimeException
{

	public const
		INVALID_CURRENT_PASSWORD = 1,
	 	USER_NOT_FOUND = 2;

}
