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
	 * @param array<string, int>|null $orderBy see {@see SqlBuilder::order()}
	 * @return array<string, mixed>|null associative array (column name => value)
	 */
	public function findOne(
		?array $where = null,
		?array $columns = null,
		?array $orderBy = null
	): ?array
	{
		$this->ensureValidTableName();

		$params = [];

		$sql = (new SqlBuilder())
			->select($this->tableName, $columns)
			->where($where, $params)
			->order($orderBy)
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

	/**
	 * Finds records (rows) in the table.
	 * @param array<string, mixed>|null $where
	 * @param array<string|int, string>|null $columns will use `*` if `null` is given, empty array not allows
	 * @param array<string, int>|null $orderBy see {@see SqlBuilder::order()}
	 * @param int|null $limit
	 * @param int|null $offset
	 * @return array<int, array<string, mixed>> numbered array of associative arrays (column name => value)
	 */
	public function find(
		?array $where = null,
		?array $columns = null,
		?array $orderBy = null,
		?int $limit = null,
		?int $offset = null
	): ?array
	{
		$this->ensureValidTableName();

		$params = [];

		$sql = (new SqlBuilder())
			->select($this->tableName, $columns)
			->where($where, $params)
			->order($orderBy)
			->limit($limit, $offset)
			->getQuery();

		$dbh = $this->connection->get();

		$sth = $dbh->prepare($sql);

		$sth->execute($params);

		return $sth->fetchAll(PDO::FETCH_ASSOC);
	}

}
