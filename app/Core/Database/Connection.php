<?php

declare(strict_types=1);

namespace Core\Database;

use PDO;

/**
 * A simple wrapper around {@see PDO} that sets some sensible defaults
 * and implements lazy (on-demand) connecting.
 */
class Connection
{

	protected const DEFAULT_OPTIONS = [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		// see https://stackoverflow.com/questions/20079320/how-do-i-return-integer-and-numeric-columns-from-mysql-as-integers-and-numerics
		PDO::ATTR_EMULATE_PREPARES => false,
		PDO::ATTR_STRINGIFY_FETCHES => false,
	];

	/**
	 * @var string The Data Source Name, or DSN, contains the information required to connect to the database.
	 * @see PDO::__construct()
	 */
	protected string $dsn;

	/**
	 * @var mixed[] A key => value array of driver-specific connection options.
	 * @see PDO::__construct()}
	 */
	protected array $options;

	protected ?PDO $dbh = null;

	/**
	 * @param string $dsn The Data Source Name, or DSN, contains the information required to connect to the database.
	 * @param mixed[] $options A key => value array of driver-specific connection options.
	 * @see PDO::__construct()
	 */
	public function __construct(string $dsn, array $options = [])
	{
		$this->dsn = $dsn;
		// the keys in $options override those in self::DEFAULT_OPTIONS
		// the order is really correct, see https://www.php.net/manual/en/language.operators.array.php
		$this->options = $options + self::DEFAULT_OPTIONS;
	}

	protected function connect(): void
	{
		// user should use dsn to provide username and password
		$this->dbh = new PDO($this->dsn, null, null, $this->options);
	}

	/**
	 * Gets the instance of PDO.
	 *
	 * When no instance instance exists yet, it is created,
	 * which causes connection to the database to be established.
	 */
	public function get(): PDO
	{
		if ($this->dbh === null) {
			$this->connect();
		}

		return $this->dbh;
	}

}
