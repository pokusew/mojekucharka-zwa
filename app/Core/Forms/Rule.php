<?php

declare(strict_types=1);

namespace Core\Forms;

class Rule
{

	const REQUIRED = 0;
	const EMAIL = 1;
	const PATTERN = 2;
	const MIN_LENGTH = 3;
	const MAX_LENGTH = 4;

	/** @var string[] */
	public static array $defaultMessage = [
		self::REQUIRED => 'Vyplňte prosím toto pole.',
		self::EMAIL => 'Prosím zadejte platnou e-mailouvou adresu.',
		self::PATTERN => 'Zadejte hodnotu, která odpovídá požadovanému formátu.',
		self::MIN_LENGTH => 'MIN_LENGTH',
		self::MAX_LENGTH => 'MAX_LENGTH',
	];

	public int $type;

	public mixed $arg;

	public string $message;

}
