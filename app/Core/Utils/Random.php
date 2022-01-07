<?php

declare(strict_types=1);

namespace Core\Utils;

use Core\Exceptions\InvalidStateException;

/**
 * Functions for generating random data.
 */
class Random
{

	/**
	 * Generates a random HEX string of the given length.
	 * @param int|null $bytesLength number of bytes
	 * @return string a random string with length of `$bytesLength * 2` chars
	 */
	public static function generateHexString(?int $bytesLength = 64): string
	{
		try {
			return bin2hex(random_bytes(64));
		} catch (\Exception $e) {
			throw new InvalidStateException(
				"Could not generate $bytesLength random bytes: " . $e->getMessage(),
				0,
				$e
			);
		}
	}

}
