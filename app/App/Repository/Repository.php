<?php

declare(strict_types=1);

namespace App\Repository;

use Core\Database\Connection;

abstract class Repository
{

	protected Connection $connection;

	public function __construct(Connection $connection)
	{
		$this->connection = $connection;
	}

}
