<?php

declare(strict_types=1);

namespace App\Repository;

use Core\Database\Repository;
use Core\Database\SqlBuilder;

class CategoriesRepository extends Repository
{

	protected string $tableName = 'categories';

	/**
	 * Finds all categories and returns them as nested array.
	 *
	 * Only one level of categories is supported.
	 *
	 * @phpstan-return array<int, array{id: int, name: string, children: array<int, array{id: int, name: string}>}>
	 */
	public function findAllNested(): array
	{
		$raw = $this->find(
			null,
			['id', 'parent_id', 'name'],
			[
				'parent_id' => SqlBuilder::ORDER_ASC, // NULL values first
				'id' => SqlBuilder::ORDER_ASC, // then by id
			]
		);

		$categories = [];

		$level = 0;
		foreach ($raw as $category) {

			if ($category['parent_id'] === null) {

				if ($level !== 0) {
					// this should never happen
					continue;
				}

				$categories[$category['id']] = [
					'id' => $category['id'],
					'name' => $category['name'],
					'children' => [],
				];

				continue;

			}

			$level = 1;

			if (!isset($categories[$category['parent_id']])) {
				// skip (probably more indirect children, i.e. more than one-level deep)
				continue;
			}

			$categories[$category['parent_id']]['children'][$category['id']] = [
				'id' => $category['id'],
				'name' => $category['name'],
			];

		}

		return $categories;
	}

}
