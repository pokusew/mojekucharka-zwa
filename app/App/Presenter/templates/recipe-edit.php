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

$title = $recipe['name'];

?>
<body class="app">

	<?php require __DIR__ . '/_header.php' ?>

	<main class="app-content">
		<div class="container">

			<h1><?= htmlspecialchars($recipe['name']) ?></h1>

			<?= $this->recipeForm->getElem()->startTag() ?>

			<?= Helpers::renderFormControl($this->recipeForm['name']) ?>

			<?= Helpers::renderFormControl($this->recipeForm['ingredients']) ?>

			<?= Helpers::renderFormControl($this->recipeForm['instructions']) ?>

			<?= $this->recipeForm['submit']->getElem()->class('btn btn-primary') ?>

			<?= $this->recipeForm->getElem()->endTag() ?>

		</div>
	</main>

	<?php require __DIR__ . '/_footer.php' ?>

</body>
