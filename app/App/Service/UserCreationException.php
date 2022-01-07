<?php

declare(strict_types=1);

namespace App\Service;

class UserCreationException extends \RuntimeException
{

	public const
		DUPLICATE_USERNAME = 1,
		DUPLICATE_EMAIL = 2;

}
