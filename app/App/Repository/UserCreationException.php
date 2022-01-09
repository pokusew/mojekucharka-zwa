<?php

declare(strict_types=1);

namespace App\Repository;

/**
 * An exception that can occur during {@see UsersRepository::createUser()}.
 */
class UserCreationException extends \RuntimeException
{

	public const
		DUPLICATE_USERNAME = 1,
		DUPLICATE_EMAIL = 2;

}
