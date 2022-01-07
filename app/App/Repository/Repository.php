<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

abstract class Repository
{

	protected PDO $dbh;

	public function __construct(PDO $dbh)
	{
		$this->dbh = $dbh;
	}

}
