<?php

declare(strict_types=1);

namespace App\Presenter;

use App\Limits;
use App\Repository\CategoriesRepository;
use App\Repository\RecipesRepository;
use Core\Exceptions\BadRequestException;
use Core\Forms\Controls\Button;
use Core\Forms\Controls\CheckBox;
use Core\Forms\Controls\Select;
use Core\Forms\Controls\TextArea;
use Core\Forms\Controls\TextInput;
use Core\Forms\Form;
use Core\Http\HttpResponse;

/**
 * @phpstan-import-type CategoriesData from CategoriesRepository
 */
class RecipePresenter extends BasePresenter
{

	/** @inject */
	public RecipesRepository $recipesRepository;

	/** @inject */
	public CategoriesRepository $categoriesRepository;

	/** @var array<string, mixed>|null */
	protected ?array $recipe = null;

	/** @phpstan-var CategoriesData */
	protected array $categories;

	protected Form $recipeForm;

	public function actionView(int $id): void
	{
		$this->view = 'recipe';

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

		$this->categories = $this->categoriesRepository->findAllAsData();

		$this->recipeForm = $this->createRecipeForm();

		$this->recipeForm->process($this->httpRequest);

		// if the form was not submitted, prefill the form with the recipe data
		$this->setRecipeFormInitialValues();
	}

	public function actionNew(): void
	{
		$this->view = 'recipe-edit';

		// user must be logged in to create their own recipes
		$this->ensureUserLoggedIn();

		$this->categories = $this->categoriesRepository->findAllAsData();

		$this->recipeForm = $this->createRecipeForm();

		$this->recipeForm->process($this->httpRequest);
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

		$categoriesOptions = [];
		$categoriesOptionsGrouped = [];

		foreach ($this->categories['nested'] as $topLevelCategory) {
			foreach ($topLevelCategory['children'] as $category) {
				$categoriesOptionsGrouped[$topLevelCategory['name']][$category['id']] = $category['name'];
				$categoriesOptions[$category['id']] = $category['name'];
			}
		}

		$form->addSelect('category', 'Kategorie')
			->setOptions($categoriesOptions)
			->setGroupedOptions($categoriesOptionsGrouped)
			->setValue((string) Limits::DEFAULT_CATEGORY);

		$form->addCheckBox('public', 'Veřejný recept');

		$form->addSubmit('edit', $this->recipe === null ? 'Vytvořit' : 'Upravit', 'edit');

		$form->addSubmit('delete', 'Smazat', 'delete');

		$form->onSuccess[] = function (Form $form) {
			$this->handleRecipeFormSuccess($form);
		};

		return $form;
	}

	private function setRecipeFormInitialValues(): void
	{
		if ($this->recipe === null) {
			return;
		}

		/** @var TextInput $name */
		$name = $this->recipeForm['name'];
		/** @var TextArea $ingredients */
		$ingredients = $this->recipeForm['ingredients'];
		/** @var TextArea $instructions */
		$instructions = $this->recipeForm['instructions'];
		/** @var Select $category */
		$category = $this->recipeForm['category'];
		/** @var CheckBox $public */
		$public = $this->recipeForm['public'];

		$name->setDefaultValue($this->recipe['name']);
		$ingredients->setDefaultValue($this->recipe['ingredients']);
		$instructions->setDefaultValue($this->recipe['instructions']);
		$category->setDefaultValue((string) $this->recipe['category_id']);
		$public->setChecked((bool) $this->recipe['public']);
	}

	private function handleRecipeFormSuccess(Form $form): void
	{
		/** @var TextInput $name */
		$name = $this->recipeForm['name'];
		/** @var TextArea $ingredients */
		$ingredients = $this->recipeForm['ingredients'];
		/** @var TextArea $instructions */
		$instructions = $this->recipeForm['instructions'];
		/** @var Select $category */
		$category = $this->recipeForm['category'];
		/** @var CheckBox $public */
		$public = $this->recipeForm['public'];
		/** @var Button $deleteButton */
		$deleteButton = $this->recipeForm['delete'];

		if ($this->recipe !== null && $deleteButton->wasUsedForSubmission()) {
			$this->recipesRepository->deleteRecipe(
				$this->recipe['id'],
				$this->getUser()->getId(),
			);
			$this->defaultRecipesRedirect();
		}

		if ($this->recipe === null) {
			// create a new recipe
			$id = $this->recipesRepository->createRecipe(
				$this->getUser()->getId(),
				$public->isChecked(),
				$name->getValue(),
				(int) $category->getValue(),
				null,
				$ingredients->getValue(),
				$instructions->getValue(),
				null,
			);
			$this->redirect('Recipe:view', ['id' => $id]);
		} else {
			// edit an existing recipe
			$this->recipesRepository->updateUsersRecipe(
				$this->recipe['id'],
				$this->getUser()->getId(),
				$public->isChecked(),
				$name->getValue(),
				(int) $category->getValue(),
				$this->recipe['main_image_id'],
				$ingredients->getValue(),
				$instructions->getValue(),
				$this->recipe['private_rating'],
			);
			$this->redirect('Recipe:view', ['id' => $this->recipe['id']]);
		}
	}

}
