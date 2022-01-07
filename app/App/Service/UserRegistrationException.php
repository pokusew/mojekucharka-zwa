<?php

declare(strict_types=1);

namespace App\Service;


class UserRegistrationException extends \RuntimeException
{

	public const
		DUPLICATE_USERNAME = 1;

}
