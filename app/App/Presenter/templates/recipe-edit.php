<?php

declare(strict_types=1);

/**
 * @var App\Presenter\RecipePresenter $this
 * @var Core\Config $config
 * @var Core\Assets $assets
 * @var Core\Routing\Router $router
 */

use App\Helpers;

$recipe = $this->recipe;

$title = $recipe !== null ? $recipe['name'] : 'Nový recept';

?>
<body class="app">

	<?php require __DIR__ . '/_header.php' ?>

	<main class="app-content">
		<div class="container">

			<h1><?= htmlspecialchars($title) ?></h1>

			<?= $this->recipeForm->getElem()->startTag() ?>

			<?= Helpers::renderFormControl($this->recipeForm['name']) ?>

			<?= Helpers::renderFormControl($this->recipeForm['ingredients']) ?>

			<?= Helpers::renderFormControl($this->recipeForm['instructions']) ?>

			<?= Helpers::renderSelect($this->recipeForm['category']) ?>

			<?= Helpers::renderFormControl($this->recipeForm['public']) ?>

			<?= $this->recipeForm['edit']->getElem()->class('btn btn-primary') ?>

			<?php if ($recipe !== null): ?>
				<?= $this->recipeForm['delete']->getElem()->class('btn btn-danger') ?>
			<?php endif; ?>

			<?= $this->recipeForm->getElem()->endTag() ?>

		</div>
	</main>

	<?php require __DIR__ . '/_footer.php' ?>

</body>
