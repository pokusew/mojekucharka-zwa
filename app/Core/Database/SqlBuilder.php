<?php

declare(strict_types=1);


namespace Core\Database;

/**
 * A very simple SQL builder.
 *
 * **NOTE:** It does NOT escape any values! It is not safe to use with any user-input!
 */
class SqlBuilder
{

	public const
		ORDER_ASC = 0,
		ORDER_DESC = 1;

	protected string $sql = '';

	/**
	 * @param string $from i.e. table name
	 * @param array<string|int, string>|null $columns
	 * @return $this
	 */
	public function select(string $from, ?array $columns = null): self
	{
		$what = '*';

		if ($columns !== null) {

			$parts = [];

			foreach ($columns as $index => $value) {

				if (is_int($index)) {
					$parts[] = $value;
					continue;
				}

				if (is_string($index)) {
					$parts[] = "$value as $index";
					continue;
				}

			}

			if (count($parts) === 0) {
				throw new \InvalidArgumentException('Invalid columns value.');
			}

			$what = join(', ', $parts);

		}

		$this->sql = "SELECT $what FROM $from";

		return $this;
	}

	/**
	 * @param array<string, mixed> $where
	 * @param string $operator
	 * @param array<string, mixed>|null $params flattened parameters
	 * @return string
	 */
	public static function condToString(array $where, string $operator, ?array &$params): string
	{
		$parts = [];

		foreach ($where as $column => $value) {

			if (is_array($value)) {
				$parts[] = '(' . self::condToString($value, $column, $params) . ')';
				continue;
			}

			if (is_string($value)) {
				$parts[] = "$column = :$column";
				if ($params !== null) {
					$params[$column] = $value;
				}
				continue;
			}

			throw new \InvalidArgumentException("Invalid value '$value' for column '$column'.");
		}

		$cond = join(strtoupper($operator) === 'OR' ? ' OR ' : ', ', $parts);

		return "$cond";
	}

	/**
	 * Adds WHERE clause.
	 *
	 * **NOTE:** The values are not used and instead placeholders in format :name are generated,
	 * so the the resulting SQL query can be used as the input for prepared statements ({@see \PDO::prepare()}).
	 *
	 * @param array<string, mixed>|null $where
	 * @param array<string, mixed>|null $params flattened parameters
	 * @return $this
	 */
	public function where(?array $where, ?array &$params = null): self
	{
		if ($where === null) {
			return $this;
		}

		if (count($where) === 0) {
			throw new \InvalidArgumentException('Invalid where value.');
		}

		$cond = self::condToString($where, 'AND', $params);

		$this->sql .= " WHERE $cond";

		return $this;
	}

	/**
	 * @param array<string, int>|null $by
	 * @return $this
	 */
	public function order(?array $by): self
	{
		if ($by === null) {
			return $this;
		}

		if (count($by) === 0) {
			throw new \InvalidArgumentException('Invalid by value.');
		}

		$parts = [];

		foreach ($by as $column => $ordering) {
			$parts[] = "$column " . ($ordering === self::ORDER_ASC ? 'ASC' : 'DESC');
		}

		$value = join(', ', $parts);

		$this->sql .= " ORDER BY $value";

		return $this;
	}

	/**
	 * @param int|null $rowCount
	 * @param int|null $offset
	 * @return $this
	 */
	public function limit(?int $rowCount, ?int $offset = null): self
	{
		if ($rowCount === null) {
			return $this;
		}

		$this->sql .= " LIMIT $rowCount";

		if ($offset !== null) {
			$this->sql .= " OFFSET $offset";
		}

		return $this;
	}

	/**
	 * Gets the current SQL query
	 * @return string the current SQL query
	 */
	public function getQuery(): string
	{
		return $this->sql;
	}

}
