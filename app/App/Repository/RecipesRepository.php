<?php

declare(strict_types=1);

namespace App\Repository;

use Core\Database\Repository;

class RecipesRepository extends Repository
{

	protected string $tableName = 'recipes';

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

}
