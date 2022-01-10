<?php

declare(strict_types=1);


namespace Core\Database;

/**
 * An SQL comparator operator.
 *
 * @see SqlBuilder::condToString()
 */
class SqlComparator
{

	public const
		EQUALS = '=',
		NOT_EQUALS = '!=';

	protected string $operator;
	/**  @var mixed */
	protected $value;

	/**
	 * @param string $operator
	 * @param mixed $value
	 */
	public function __construct(string $operator, $value)
	{
		$this->operator = $operator;
		$this->value = $value;
	}

	public function getOperator(): string
	{
		return $this->operator;
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @param mixed $value
	 */
	public static function equals($value): SqlComparator
	{
		return new SqlComparator(self::EQUALS, $value);
	}

	/**
	 * @param mixed $value
	 */
	public static function notEquals($value): SqlComparator
	{
		return new SqlComparator(self::NOT_EQUALS, $value);
	}

}
