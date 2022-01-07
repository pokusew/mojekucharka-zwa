<?php

declare(strict_types=1);

namespace Core\Utils;

use Core\Exceptions\InvalidStateException;
use InvalidArgumentException;

/**
 * A simple objective wrapper around PHP password_* functions.
 *
 * It has built-in error handling.
 * It is designed to be used with DI.
 */
class Passwords
{

	private string $algo;
	/** @var array<string, mixed> */
	private array $options;

	/**
	 * Chooses which secure algorithm is used for hashing and how to configure it.
	 * @see https://php.net/manual/en/password.constants.php
	 * @param string $algo
	 * @param array<string, mixed> $options
	 */
	public function __construct(string $algo = PASSWORD_DEFAULT, array $options = [])
	{
		$this->algo = $algo;
		$this->options = $options;
	}

	/**
	 * Computes password's hash. The result contains the algorithm ID and its settings,
	 * cryptographical salt and the hash itself.
	 */
	public function hash(string $password): string
	{
		if ($password === '') {
			throw new InvalidArgumentException('Password must not be empty.');
		}

		$hash = @password_hash($password, $this->algo, $this->options); // @ is escalated to exception

		if (!$hash) {
			throw new InvalidStateException('Computed hash is invalid: ' . error_get_last()['message']);
		}

		return $hash;
	}

	/**
	 * Checks if the given hash matches the given options.
	 * @link https://secure.php.net/manual/en/function.password-verify.php
	 * @param string $password The user's password.
	 * @param string $hash A hash created by password_hash().
	 * @return bool Returns TRUE if the password and hash match, or FALSE otherwise.
	 */
	public function verify(string $password, string $hash): bool
	{
		return password_verify($password, $hash);
	}

	/**
	 * Checks if the given hash matches the options given in the constructor.
	 */
	public function needsRehash(string $hash): bool
	{
		return password_needs_rehash($hash, $this->algo, $this->options);
	}

}
