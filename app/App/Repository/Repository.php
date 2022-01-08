<?php

declare(strict_types=1);

namespace App\Repository;

use Core\Database\Connection;
use Core\Database\SqlBuilder;
use Core\Exceptions\InvalidStateException;
use PDO;

abstract class Repository
{

	protected string $tableName = '';

	protected Connection $connection;

	public function __construct(Connection $connection)
	{
		$this->connection = $connection;
	}

	protected function ensureValidTableName(): void
	{
		if ($this->tableName === '') {
			throw new InvalidStateException('Table name must not be empty.');
		}
	}

	/**
	 * @param array<string, mixed>|null $where
	 * @param array<string|int, string>|null $columns will use `*` if `null` is given, empty array not allows
	 * @return array<string, mixed>|null
	 */
	public function findOne(?array $where = null, ?array $columns = null): ?array
	{
		$this->ensureValidTableName();

		$params = [];

		$sql = (new SqlBuilder())
			->select($this->tableName, $columns)
			->where($where, $params)
			->limit(1)
			->getQuery();

		$dbh = $this->connection->get();

		$sth = $dbh->prepare($sql);

		$sth->execute($params);

		$result = $sth->fetch(PDO::FETCH_ASSOC);

		if ($result === false) {
			return null;
		}

		return $result;

	}

}
