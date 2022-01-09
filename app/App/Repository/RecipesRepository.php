<?php

declare(strict_types=1);

namespace App\Repository;

use Core\Database\Repository;
use Core\Database\SqlBuilder;

class RecipesRepository extends Repository
{

	public const TABLE = 'recipes';

	/**
	 * Creates a new recipe with the given parameters.
	 */
	public function createRecipe(
		int $userId,
		bool $public,
		string $name,
		?int $categoryId,
		?int $mainImageId,
		string $ingredients,
		string $instructions,
		?int $privateRating
	): int
	{
		$dbh = $this->connection->get();

		$sth = $dbh->prepare(<<<'SQL'
			INSERT INTO recipes (
				user_id,
				created_at,
				public,
				name,
				category_id,
				main_image_id,
				ingredients,
				instructions,
				private_rating
			)
			VALUES
				(
					:userId,
					NOW(),
					:public,
					:name,
					:categoryId,
					:mainImageId,
					:ingredients,
					:instructions,
					:privateRating
				)
			;
			SQL
		);

		$sth->execute([
			'userId' => $userId,
			'public' => $public,
			'name' => $name,
			'categoryId' => $categoryId,
			'mainImageId' => $mainImageId,
			'ingredients' => $ingredients,
			'instructions' => $instructions,
			'privateRating' => $privateRating,
		]);

		return (int) $dbh->lastInsertId();

	}

	/**
	 * Updates the recipe with the given id that belongs to the user with userId.
	 */
	public function updateUsersRecipe(
		int $id,
		int $userId,
		bool $public,
		string $name,
		?int $categoryId,
		?int $mainImageId,
		string $ingredients,
		string $instructions,
		?int $privateRating
	): bool
	{
		$dbh = $this->connection->get();

		$sth = $dbh->prepare(<<<'SQL'
			UPDATE recipes SET
				public = :public,
				name = :name,
				category_id = :category_id,
				main_image_id = :main_image_id,
				ingredients = :ingredients,
				instructions = :instructions,
				private_rating = :private_rating
			WHERE id = :id AND user_id = :user_id
			SQL
		);

		$sth->execute([
			'id' => $id,
			'userId' => $userId,
			'public' => $public,
			'name' => $name,
			'categoryId' => $categoryId,
			'mainImageId' => $mainImageId,
			'ingredients' => $ingredients,
			'instructions' => $instructions,
			'privateRating' => $privateRating,
		]);

		return $sth->rowCount() === 1;

	}

	/**
	 * Creates a new instance of SQL builder with SELECT and JOIN(s).
	 * @return SqlBuilder
	 */
	private function selectWithJoinCategoryAndUserAndMainImage(): SqlBuilder
	{
		return $this->getSqlBuilder()
			->select(
				self::TABLE,
				[
					self::TABLE . '.*',
					'`category.id`' => 'categories.id',
					'`category.parent_id`' => 'categories.parent_id',
					'`category.name`' => 'categories.name',
					'`user.id`' => 'users.id',
					'`user.name`' => 'users.name',
					'`user.username`' => 'users.username',
					'`main_image.id`' => 'images.id',
					'`main_image.name`' => 'images.name',
				],
			)
			->leftJoin(CategoriesRepository::TABLE, 'recipes.category_id = categories.id')
			->leftJoin(UsersRepository::TABLE, 'recipes.user_id = users.id')
			->leftJoin(ImagesRepository::TABLE, 'recipes.main_image_id = images.id');
	}

	/**
	 * Finds all matching recipes and joins info about category, user and main image.
	 *
	 * **NOTE:** All column names in $where and $orderBy must be prefixed by correct table name.
	 *
	 * @param array<string, mixed>|null $where
	 * @param array<string, int>|null $orderBy see {@see SqlBuilder::order()}
	 * @param int|null $limit
	 * @param int|null $offset
	 * @return array<int, array<string, mixed>> numbered array of associative arrays (column name => value)
	 */
	public function findAndJoinCategoryAndUserAndMainImage(
		?array $where = null,
		?array $orderBy = null,
		?int $limit = null,
		?int $offset = null
	): ?array
	{
		$params = [];

		$query = $this->selectWithJoinCategoryAndUserAndMainImage()
			->where($where, $params)
			->order($orderBy)
			->limit($limit, $offset)
			->getQuery();

		return $this->fetchAllAssoc($query, $params);
	}

	/**
	 * Finds one matching recipe and joins info about category, user and main image.
	 *
	 * **NOTE:** All column names in $where and $orderBy must be prefixed by correct table name.
	 *
	 * @param array<string, mixed>|null $where
	 * @param array<string, int>|null $orderBy see {@see SqlBuilder::order()}
	 * @return array<string, mixed>|null associative array (column name => value), `null` when there is no result
	 */
	public function findOneAndJoinCategoryAndUserAndMainImage(
		?array $where = null,
		?array $orderBy = null
	): ?array
	{
		$params = [];

		$query = $this->selectWithJoinCategoryAndUserAndMainImage()
			->where($where, $params)
			->order($orderBy)
			->limit(1)
			->getQuery();

		return $this->fetchOneAssoc($query, $params);
	}

}
