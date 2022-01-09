<?php

declare(strict_types=1);

namespace Core\Database;

use PDO;
use RuntimeException;

/**
 * Contains useful methods for working with a specific table.
 */
abstract class Repository
{

	const TABLE = '';

	protected Connection $connection;

	public function __construct(Connection $connection)
	{
		$this->connection = $connection;
	}

	/**
	 * Gets a new instance of the SQL builder
	 * @return SqlBuilder a new instance
	 */
	public function getSqlBuilder(): SqlBuilder
	{
		return new SqlBuilder();
	}

	/**
	 * Prepares the given query, executes it with the given params
	 * and fetches one rows with `fetch(PDO::FETCH_ASSOC)`.
	 * @param string $query raw SQL query but with placeholders
	 * @param array<string, mixed>|null $params values for placeholders
	 * @return array<string, mixed>|null associative array (column name => value), `null` when there is no result
	 */
	public function fetchOneAssoc(string $query, ?array $params): ?array
	{
		$dbh = $this->connection->get();

		$sth = $dbh->prepare($query);

		$sth->execute($params);

		$result = $sth->fetch(PDO::FETCH_ASSOC);

		if ($result === false) {
			return null;
		}

		return $result;
	}

	/**
	 * Prepares the given query, executes it with the given params
	 * and fetches all rows with `fetchAll(PDO::FETCH_ASSOC)`.
	 * @param string $query raw SQL query but with placeholders
	 * @param array<string, mixed>|null $params values for placeholders
	 * @return array<int, array<string, mixed>> numbered array of associative arrays (column name => value)
	 */
	public function fetchAllAssoc(string $query, ?array $params): array
	{
		$dbh = $this->connection->get();

		$sth = $dbh->prepare($query);

		$sth->execute($params);

		return $sth->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Finds one record (row) in the table.
	 * @param array<string, mixed>|null $where
	 * @param array<string|int, string>|null $columns will use `*` if `null` is given, empty array not allowed
	 * @param array<string, int>|null $orderBy see {@see SqlBuilder::order()}
	 * @return array<string, mixed>|null associative array (column name => value), `null` when there is no result
	 */
	public function findOne(
		?array $where = null,
		?array $columns = null,
		?array $orderBy = null
	): ?array
	{
		$params = [];

		$query = $this->getSqlBuilder()
			->select(static::TABLE, $columns)
			->where($where, $params)
			->order($orderBy)
			->limit(1)
			->getQuery();

		return $this->fetchOneAssoc($query, $params);
	}

	/**
	 * Finds records (rows) in the table.
	 * @param array<string, mixed>|null $where
	 * @param array<string|int, string>|null $columns will use `*` if `null` is given, empty array not allowed
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
		$params = [];

		$query = $this->getSqlBuilder()
			->select(static::TABLE, $columns)
			->where($where, $params)
			->order($orderBy)
			->limit($limit, $offset)
			->getQuery();

		return $this->fetchAllAssoc($query, $params);
	}

	/**
	 * Counts records (rows) in the table.
	 * @param array<string, mixed>|null $where
	 * @param ?string $column the column name to use in COUNT(), set to `*` on `null`
	 * @return int number of rows
	 */
	public function count(
		?array $where = null,
		?string $column = null
	): int
	{
		$value = $column ?? '*';

		$params = [];

		$query = $this->getSqlBuilder()
			->select(static::TABLE, ['total' => "COUNT($value)"])
			->where($where, $params)
			->getQuery();

		$result = $this->fetchOneAssoc($query, $params);

		if (!is_int($result['total'])) {
			throw new RuntimeException('Unexpected result for count query.');
		}

		return $result['total'];
	}

}
