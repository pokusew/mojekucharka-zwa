<?php

declare(strict_types=1);

namespace Core\Database;

use Core\Exceptions\InvalidStateException;
use PDO;

/**
 * Contains useful methods for working with a specific table.
 */
abstract class Repository
{

	protected string $tableName = '';

	protected Connection $connection;

	public function __construct(Connection $connection)
	{
		$this->connection = $connection;
	}

	/**
	 * Ensures that the {@see Repository::$tableName} is not empty (i.e. it was set by the subclass).
	 * @throws InvalidStateException if the tableName is empty
	 */
	protected function ensureValidTableName(): void
	{
		if ($this->tableName === '') {
			throw new InvalidStateException('Table name must not be empty.');
		}
	}

	/**
	 * Finds one record (row) in the table.
	 * @param array<string, mixed>|null $where
	 * @param array<string|int, string>|null $columns will use `*` if `null` is given, empty array not allows
	 * @return array<string, mixed>|null associative array (column name => value)
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
