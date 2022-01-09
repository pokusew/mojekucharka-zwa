<?php

declare(strict_types=1);

namespace App\Presenter;

use App\Repository\CategoriesRepository;
use App\Repository\RecipesRepository;
use Core\Database\SqlBuilder;
use Core\Utils\Paginator;

/**
 * @phpstan-import-type CategoriesData from CategoriesRepository
 */
class RecipesPresenter extends BasePresenter
{

	/** @inject */
	public CategoriesRepository $categoriesRepository;

	/** @inject */
	public RecipesRepository $recipesRepository;

	/** @phpstan-var CategoriesData */
	protected array $categories;

	/** @var mixed[] */
	protected array $recipes;

	protected Paginator $paginator;

	public function __construct()
	{
		$this->view = 'recipes';
	}

	public function action(): void
	{
		$this->categories = $this->categoriesRepository->findAllAsData();

		$this->paginator = new Paginator();

		// TODO: get from query
		$filter = null;
		$itemsPerPage = 20;
		$pageNumber = 1;

		$this->paginator->setItemsPerPage($itemsPerPage);
		$this->paginator->setPageNumber($pageNumber);
		$this->paginator->setItemsCount($this->recipesRepository->count($filter));

		$this->recipes = $this->recipesRepository->findAndJoinCategoryAndUserAndMainImage(
			null,
			[
				RecipesRepository::TABLE . '.name' => SqlBuilder::ORDER_ASC,
			],
			$this->paginator->getLimit(),
			$this->paginator->getOffset(),
		);
	}

}
