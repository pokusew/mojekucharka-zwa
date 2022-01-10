<?php

declare(strict_types=1);

namespace App\Presenter;

use App\RecipesFilter;
use App\Repository\CategoriesRepository;
use App\Repository\RecipesRepository;
use App\Security\SecurityException;
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

	protected RecipesFilter $filter;

	/**
	 * Merges the given query with the current query and returns the corresponding link.
	 * @param array<string, mixed> $query
	 * @return string
	 */
	public function recipesLink(array $query = []): string
	{
		$merged = $query + $this->filter->getQuery();
		return $this->link('this', null, false, $merged);
	}

	/**
	 * Merges the given query with the current query and redirects to the corresponding link.
	 * @param array<string, mixed> $query
	 * @return never
	 */
	public function recipesRedirect(array $query = []): string
	{
		$merged = $query + $this->filter->getQuery();
		$this->redirect('this', null, false, $merged);
	}

	public function action(): void
	{
		$this->view = 'recipes';

		$this->categories = $this->categoriesRepository->findAllAsData();

		$this->filter = new RecipesFilter($this->categories);

		if (!$this->filter->setFromQuery($this->httpRequest->query)) {
			$this->defaultRecipesRedirect();
		}

		$this->paginator = new Paginator();

		try {
			$where = $this->filter->getWhere($this->isUserLoggedIn() ? $this->getUser() : null);
		} catch (SecurityException $e) {
			// TODO: add reason and backUrl
			$this->redirect('SignIn:');
		}

		$this->paginator->setItemsPerPage(20); // TODO: make configurable
		$this->paginator->setItemsCount($this->recipesRepository->count($where));

		if (!$this->paginator->isValidPageNumber($this->filter->getPage())) {
			$this->recipesRedirect([
				'page' => $this->paginator->getFirstPageNumber()
			]);
		}

		$this->paginator->setPageNumber($this->filter->getPage());

		$this->recipes = $this->recipesRepository->findAndJoinCategoryAndUserAndMainImage(
			$where,
			$this->filter->getOrderBy(),
			$this->paginator->getLimit(),
			$this->paginator->getOffset(),
		);
	}

}
