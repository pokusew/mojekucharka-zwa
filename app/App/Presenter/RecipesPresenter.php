<?php

declare(strict_types=1);

namespace App\Presenter;

use PDO;
use PDOException;

class RecipesPresenter extends BasePresenter
{

	public function __construct()
	{
		$this->view = 'recipes';
	}

	public function action(): void
	{
		// just a database demo
		try {
			$dbh = new PDO($this->config->parameters['databaseDsn']);
			if (!$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION)) {
				throw new \RuntimeException('Could not set PDO::ATTR_ERRMODE to PDO::ERRMODE_EXCEPTION.');
			}
			foreach ($dbh->query('SELECT * from FOO') as $row) {
				dump($row);
			}
			$dbh = null;
		} catch (PDOException $e) {
			// let the core app handle the exception
			throw $e;
		}
	}

}
