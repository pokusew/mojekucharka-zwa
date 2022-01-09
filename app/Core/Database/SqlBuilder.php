<?php

declare(strict_types=1);


namespace Core\Database;

/**
 * A very simple SQL builder.
 *
 * **NOTE:** It does NOT escape any values! It is not safe to use with any user-input!
 *           But it some methods use placeholders for values (see for example {@see SqlBuilder::where()})).
 */
class SqlBuilder
{

	/**
	 * Allows values for
	 */
	public const
		ORDER_ASC = 0,
		ORDER_DESC = 1;

	protected string $sql = '';

	/**
	 * Starts query building with SELECT.
	 * @param string $from i.e. table name, or table name AS something
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
	 * Adds $type JOIN $what ON $on.
	 * @param string $type
	 * @param string $what
	 * @param string $on
	 * @return $this
	 */
	public function join(string $type, string $what, string $on): self
	{
		$this->sql .= " $type JOIN $what ON $on";
		return $this;
	}

	/**
	 * Adds LEFT JOIN $what ON $on.
	 * @param string $what
	 * @param string $on
	 * @return $this
	 */
	public function leftJoin(string $what, string $on): self
	{
		return $this->join('LEFT', $what, $on);
	}

	/**
	 * Converts the given column name so that it can be used as a placeholder name.
	 * @param string $column table column name
	 * @return void a name that can be used as a placeholder name
	 */
	public static function columnNameToPlaceholder(string $column): string /* @phpstan-ignore-line */
	{
		return preg_replace('/[^a-zA-Z0-9_]/', '_', $column);
	}

	/**
	 * Recursively traverses the $where associative array and builds the condition.
	 *
	 * Examples:
	 * ```php
	 * $whereSimple = ['email' => $email, 'active' => true];
	 * // returns `email = :email AND active = :active`
	 * // and sets $params['email'] = $email
	 * // and sets $params['active'] = true
	 *
	 * $whereOr = [
	 *   'active' => true,
	 *   'OR' => [
	 *     'email' => $emailOrUsername,
	 *     'username' => $emailOrUsername,
	 *   ],
	 * ];
	 * // returns `active = :active AND (email = :email OR username = :username)`
	 * // and sets $params['active'] = true
	 * // and sets $params['email'] = $emailOrUsername
	 * // and sets $params['username'] = $emailOrUsername
	 * ```
	 *
	 * @param array<string, mixed> $where
	 * @param string $operator logical operator that will be used between the columns of $where, either `AND` or `OR`
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

			$placeholder = self::columnNameToPlaceholder($column);
			$parts[] = "$column = :$placeholder";
			if ($params !== null) {
				$params[$placeholder] = $value;
			}
		}

		$cond = join(strtoupper($operator) === 'OR' ? ' OR ' : ' AND ', $parts);

		return "$cond";
	}

	/**
	 * Adds WHERE clause.
	 *
	 * **NOTE:** The values are not used and instead placeholders in format :name are generated,
	 * so the the resulting SQL query can be used as the input for prepared statements ({@see \PDO::prepare()}).
	 *
	 * @param array<string, mixed>|null $where see {@see SqlBuilder::condToString()}
	 * @param array<string, mixed>|null $params a reference to an array, flattened parameters with their values
	 *                                          will be added
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
	 * Adds ORDER BY column1 ASC|DESC, column2 ASC|DESC, ..., columnN ASC|DESC clause.
	 * @param array<string, int>|null $by associate ((column name => ordering) ordered array
	 * @return $this
	 * @see SqlBuilder::ORDER_ASC
	 * @see SqlBuilder::ORDER_DESC
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
	 * Adds LIMIT $rowCount [ OFFSET $offset] clause.
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

		if ($offset !== null && $offset !== 0) {
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
