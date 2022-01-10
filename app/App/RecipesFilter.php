<?php

declare(strict_types=1);

namespace App;

use App\Repository\CategoriesRepository;
use App\Repository\RecipesRepository;
use App\Repository\UsersRepository;
use App\Security\SecurityException;
use App\Security\SessionUser;
use Core\Database\SqlBuilder;
use Core\Database\SqlComparator;

/**
 * @phpstan-import-type CategoriesData from CategoriesRepository
 * @phpstan-import-type Category from CategoriesRepository
 */
class RecipesFilter
{

	public const DEFAULT_NOT_LOGGED_IN_QUERY = [
		'owner' => self::OWNER_ALL,
		'sort' => self::SORT_NAME,
		'order' => SqlBuilder::ORDER_ASC,
		'page' => 1,
	];

	public const DEFAULT_LOGGED_IN_QUERY = [
		'owner' => self::OWNER_ME,
		'sort' => self::SORT_NAME,
		'order' => SqlBuilder::ORDER_ASC,
		'page' => 1,
	];

	public const
		SORT_NAME = 'name',
		SORT_USER = 'user',
		SORT_CREATED = 'created',
		SORT_CHANGED = 'changed';
	public const ALLOWED_SORT = [
		self::SORT_NAME, self::SORT_USER, self::SORT_CREATED, self::SORT_CHANGED,
	];
	public const SORT_TO_ORDER_BY_MAP = [
		'name' => RecipesRepository::TABLE . '.name',
		'user' => UsersRepository::TABLE . '.username',
		'created' => RecipesRepository::TABLE . '.created_at',
		'changed' => RecipesRepository::TABLE . '.changed_at',
	];
	public const ORDER_BY_TO_SORT_MAP = [
		RecipesRepository::TABLE . '.name' => 'name',
		UsersRepository::TABLE . '.username' => 'user',
		RecipesRepository::TABLE . '.created_at' => 'created',
		RecipesRepository::TABLE . '.changed_at' => 'changed',
	];

	public const
		OWNER_ALL = 'all',
		OWNER_ME = 'me',
		OWNER_OTHERS = 'others';
	public const ALLOWED_OWNER = [
		self::OWNER_ALL, self::OWNER_ME, self::OWNER_OTHERS,
	];

	/**
	 * @phpstan-var CategoriesData
	 * @see CategoriesRepository::findAllAsData()
	 */
	protected array $categories;

	/**
	 * @phpstan-param CategoriesData $categories
	 */
	public function __construct(array $categories)
	{
		$this->categories = $categories;
	}

	/**
	 * @phpstan-var 'all'|'me'|'others'
	 * @var string
	 */
	protected string $owner = self::OWNER_ALL;
	protected string $sort = self::SORT_NAME;
	protected int $order = SqlBuilder::ORDER_ASC;

	/** @var Category|null */
	protected ?array $category = null;

	protected int $page = 1;

	/**
	 * @var array<string, string|int>|null
	 */
	protected ?array $query = null;

	/**
	 * Sets the filter from the query params.
	 * @param array<string, mixed> $query
	 * @return bool `true` on success, `false` on an invalid value (no state is changed)
	 */
	public function setFromQuery(array $query): bool
	{
		// owner (required)
		if (isset($query['owner']) && in_array($query['owner'], self::ALLOWED_OWNER, true)) {
			$owner = $query['owner'];
		} else {
			return false;
		}

		// sort (required)
		if (isset($query['sort']) && in_array($query['sort'], self::ALLOWED_SORT, true)) {
			$sort = $query['sort'];
		} else {
			return false;
		}

		// order (required)
		if (
			isset($query['order'])
			&& (
				$query['order'] === (string) SqlBuilder::ORDER_ASC
				|| $query['order'] === (string) SqlBuilder::ORDER_DESC
			)
		) {
			$order = (int) $query['order'];
		} else {
			return false;
		}

		// category (optional)
		if (isset($query['category'])) {
			if (!is_numeric($query['category'])) {
				return false;
			}
			$categoryId = (int) $query['category'];
			$category = $this->categories['map'][$categoryId] ?? null;
			// only child categories are supported (top-level are not)
			if ($category === null || !isset($category['parent_id'])) {
				return false;
			}
		}

		// page (required)
		if (isset($query['page'])) {
			if (!is_numeric($query['page'])) {
				return false;
			}
			$page = (int) $query['page'];
		} else {
			return false;
		}

		$this->owner = $owner;
		$this->sort = $sort;
		$this->order = $order;
		$this->category = $category ?? null;
		$this->page = $page;

		$this->query = null;

		return true;
	}

	/**
	 * Returns this filter as query params
	 * @return array<string, string|int>
	 */
	public function getQuery(): array
	{
		// no need to rebuild, return cached version
		if ($this->query !== null) {
			return $this->query;
		}

		$this->query = [
			'owner' => $this->owner,
			'sort' => $this->sort,
			'order' => $this->order,
		];

		if ($this->category !== null) {
			$this->query['category'] = $this->category['id'];
		}

		if ($this->page !== null) {
			$this->query['page'] = $this->page;
		}

		return $this->query;
	}

	/**
	 * @return string
	 */
	public function getOwner(): string
	{
		return $this->owner;
	}

	/**
	 * @return Category|null
	 */
	public function getCategory(): ?array
	{
		return $this->category;
	}

	/**
	 * @return int
	 */
	public function getPage(): int
	{
		return $this->page;
	}

	/**
	 * Builds the SQL WHERE conditions matching this filter.
	 * @param SessionUser|null $user
	 * @return array<string, mixed>|null
	 * @throws SecurityException when the given user cannot execute this filter
	 */
	public function getWhere(?SessionUser $user): ?array
	{
		if ($user === null && $this->owner !== self::OWNER_ALL) {
			throw new SecurityException("User must be logged in to use filter owner='$this->owner'.");
		}

		$where = [
			'AND' => [],
		];

		if ($this->owner === self::OWNER_ME) {

			$where['AND'][RecipesRepository::TABLE . '.user_id'] = $user->getId();

		} else if ($this->owner === self::OWNER_OTHERS) {

			$where['AND'][RecipesRepository::TABLE . '.user_id'] = SqlComparator::notEquals($user->getId());
			$where['AND'][RecipesRepository::TABLE . '.public'] = 1;


		} else if ($this->owner === self::OWNER_ALL) {

			if ($user !== null) {
				$where['AND']['OR'] = [
					RecipesRepository::TABLE . '.user_id' => $user->getId(),
					RecipesRepository::TABLE . '.public' => 1,
				];
			} else {
				$where['AND'][RecipesRepository::TABLE . '.public'] = 1;
			}

		}

		if ($this->category !== null) {

			$where['AND'][RecipesRepository::TABLE . '.category_id'] = $this->category['id'];

		}

		return $where;
	}

	/**
	 * Build the column list for the SQL ORDER BY matching this filter.
	 * @return array<string, int>|null
	 */
	public function getOrderBy(): ?array
	{
		return [
			self::SORT_TO_ORDER_BY_MAP[$this->sort] => $this->order,
		];
	}

	public function getSort(): string
	{
		return $this->sort;
	}

	public function getOrder(): int
	{
		return $this->order;
	}

}
