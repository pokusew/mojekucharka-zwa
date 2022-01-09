<?php

declare(strict_types=1);

namespace App\Service;

/**
 * An exception that can occur during {@see UsersService::registerUser()}.
 */
class UserRegistrationException extends \RuntimeException
{

	public const
		DUPLICATE_USERNAME = 1;

}
