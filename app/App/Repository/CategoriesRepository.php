<?php

declare(strict_types=1);

namespace App\Repository;

use Core\Database\Repository;
use Core\Database\SqlBuilder;

/**
 * @phpstan-type Category array{id: int, parent_id: int|null, name: string}
 * @phpstan-type CategoryWithChildren array{id: int, parent_id: int|null, name: string, children: array<int, Category>}
 * @phpstan-type CategoriesData array{map: array<int, Category>, nested: array<int, CategoryWithChildren>}
 */
class CategoriesRepository extends Repository
{

	public const TABLE = 'categories';

	/**
	 * Finds all categories and returns them nested array and id to category map.
	 *
	 * Only one level of categories is supported.
	 *
	 * @phpstan-return CategoriesData
	 */
	public function findAllAsData(): array
	{
		$raw = $this->find(
			null,
			['id', 'parent_id', 'name'],
			[
				'parent_id' => SqlBuilder::ORDER_ASC, // NULL values first
				'id' => SqlBuilder::ORDER_ASC, // then by id
			]
		);

		$map = [];
		$nested = [];

		$level = 0;
		foreach ($raw as $category) {

			if ($category['parent_id'] === null) {

				if ($level !== 0) {
					// this should never happen
					continue;
				}

				$nested[$category['id']] = $category;
				$nested[$category['id']]['children'] = [];
				$map[$category['id']] = &$nested[$category['id']];

				continue;

			}

			$level = 1;

			if (!isset($nested[$category['parent_id']])) {
				// skip (probably more indirect children, i.e. more than one-level deep)
				continue;
			}

			$nested[$category['parent_id']]['children'][$category['id']] = $category;
			$map[$category['id']] = &$nested[$category['parent_id']]['children'][$category['id']];

		}

		return [
			'nested' => $nested,
			'map' => $map,
		];
	}

}
