<?php

declare(strict_types=1);

namespace App\Presenter;

use App\Limits;
use App\Repository\CategoriesRepository;
use App\Repository\RecipesRepository;
use Core\Exceptions\BadRequestException;
use Core\Forms\Controls\TextArea;
use Core\Forms\Controls\TextInput;
use Core\Forms\Form;
use Core\Http\HttpResponse;

class RecipePresenter extends BasePresenter
{

	/** @inject */
	public RecipesRepository $recipesRepository;

	/** @inject */
	public CategoriesRepository $categoriesRepository;

	/** @var array<string, mixed> */
	protected array $recipe;

	protected Form $recipeForm;

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

		$this->recipeForm = $this->createRecipeForm();

		$this->recipeForm->process($this->httpRequest);

		// if the form was not submitted, prefill the form with the recipe data
		$this->setRecipeFormInitialValues();
	}

	private function createRecipeForm(): Form
	{
		$form = new Form('recipe');

		$form->setAction($this->link('this'));

		$form->addText('name', 'Název')
			->setPlaceholder('Název recptu')
			->setRequired()
			->setMinLength(Limits::RECIPE_MIN_LENGTH)
			->setMaxLength(Limits::RECIPE_MAX_LENGTH);

		$form->addTextArea('ingredients', 'Suroviny')
			->getElem()->setAttribute('rows', 10);

		$form->addTextArea('instructions', 'Postup')
			->getElem()->setAttribute('rows', 10);

		$form->addSubmit('submit', 'Upravit');

		$form->onSuccess[] = function (Form $form) {
			$this->handleRecipeFormSuccess($form);
		};

		return $form;
	}

	private function setRecipeFormInitialValues(): void
	{
		/** @var TextInput */
		$name = $this->recipeForm['name'];
		/** @var TextArea */
		$ingredients = $this->recipeForm['ingredients'];
		/** @var TextArea */
		$instructions = $this->recipeForm['instructions'];

		$name->setDefaultValue($this->recipe['name']);
		$ingredients->setDefaultValue($this->recipe['ingredients']);
		$instructions->setDefaultValue($this->recipe['instructions']);
	}

	private function handleRecipeFormSuccess(Form $form): void
	{
		/** @var TextInput */
		$name = $form['name'];
		/** @var TextArea */
		$ingredients = $form['ingredients'];
		/** @var TextArea */
		$instructions = $form['instructions'];
		// $category = $form['category'];
		// $public = $form['public'];

		// TODO
		$this->recipesRepository->updateUsersRecipe(
			$this->recipe['id'],
			$this->getUser()->getId(),
			(bool) $this->recipe['public'],
			$name->getValue(),
			$this->recipe['category_id'],
			$this->recipe['main_image_id'],
			$ingredients->getValue(),
			$instructions->getValue(),
			$this->recipe['private_rating'],
		);

		$this->redirect('Recipe:view', ['id' => $this->recipe['id']]);
	}

}
