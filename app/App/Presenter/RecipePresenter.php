<?php

declare(strict_types=1);

namespace App\Presenter;

use App\Repository\CategoriesRepository;
use App\Repository\RecipesRepository;
use Core\Exceptions\BadRequestException;
use Core\Http\HttpResponse;

class RecipePresenter extends BasePresenter
{

	/** @inject */
	public RecipesRepository $recipesRepository;

	/** @inject */
	public CategoriesRepository $categoriesRepository;

	/** @var array<string, mixed> */
	protected array $recipe;

	public function __construct()
	{
		$this->view = 'recipe';
	}

	public function actionView(int $id): void
	{
		$userId = $this->isUserLoggedIn() ? $this->getUser()->getId() : null;

		if ($userId === null) {
			// NOT-logged-in user can view only public recipes
			$filter = [
				RecipesRepository::TABLE . '.id' => $id,
				RecipesRepository::TABLE . '.public' => true,
			];
		} else {
			// logged-in user can view public recipes and also their own recipes (incl. private)
			$filter = [
				RecipesRepository::TABLE . '.id' => $id,
				'OR' => [
					RecipesRepository::TABLE . '.public' => true,
					RecipesRepository::TABLE . '.user_id' => $userId,
				],
			];
		}

		$recipe = $this->recipesRepository->findOneAndJoinCategoryAndUserAndMainImage($filter);

		if ($recipe === null) {
			throw new BadRequestException(
				"Recipe with id '$id' not found.",
				HttpResponse::S_404_NOT_FOUND,
			);
		}

		$this->recipe = $recipe;
	}

	public function actionEdit(int $id): void
	{
		$this->view = 'recipe-edit';

		// user must be logged in to edit their own recipes
		$this->ensureUserLoggedIn();

		// user can edit only their own recipes
		$recipe = $this->recipesRepository->findOneAndJoinCategoryAndUserAndMainImage([
			RecipesRepository::TABLE . '.id' => $id,
			RecipesRepository::TABLE . '.user_id' => $this->getUser()->getId(),
		]);

		if ($recipe === null) {
			throw new BadRequestException(
				"Recipe with id '$id' not found.",
				HttpResponse::S_404_NOT_FOUND,
			);
		}

		$this->recipe = $recipe;
	}

}
