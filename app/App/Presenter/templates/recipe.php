<?php

declare(strict_types=1);

/**
 * @var App\Presenter\RecipePresenter $this
 * @var Core\Config $config
 * @var Core\Assets $assets
 * @var Core\Routing\Router $router
 */

use App\Helpers;
use App\Icons;
use Core\Template\Html;

$recipe = $this->recipe;

$title = $recipe['name'];

$isUsersOwnRecipe = $this->isUserLoggedIn() && $recipe['user_id'] === $this->getUser()->getId();

$createdAt = Helpers::ds($recipe['created_at']);
$changedAt = isset($recipe['changed_at']) ? Helpers::ds($recipe['changed_at']) : null;

$public = (bool) $recipe['public'];

?>
<body class="app">

	<?php require __DIR__ . '/_header.php' ?>

	<main class="app-content recipe" itemscope itemtype="http://schema.org/Recipe">
		<div class="container">

			<h1 itemprop="name"><?= htmlspecialchars($recipe['name']) ?></h1>

			<div
				itemprop="recipeCategory"
				class="recipe-category"
			><?= htmlspecialchars($recipe['category.name']) ?></div>

			<?php if ($isUsersOwnRecipe): ?>

				<p class="recipe-dates">
					Přidáno <?= Helpers::timeEl($createdAt) ?>.
					<?php if (isset($changedAt)): ?>
						<br />Naposledy upraveno <?= Helpers::timeEl($changedAt) ?>.
					<?php endif; ?>
				</p>

				<div <?= Html::attrClass('recipe-visibility', ['public' => $public]) ?>>
					<span class="label">Veřejný:</span>
					<?= $public ? Icons::FA_GLOBAL_EUROPA_DUOTONE : Icons::FA_LOCK_DUOTONE ?>
					<span class="value"><?= $public ? 'ano' : 'ne' ?></span>
				</div>

				<div class="recipe-tools">
					<a href="<?= $this->link('Recipe:edit', ['id' => $recipe['id']]) ?>" class="btn btn-primary">
						Upravit recept
					</a>
					<button type="button" class="btn btn-print">Tisknout recept</button>
				</div>

			<?php else: ?>

				<p>
					Přidal uživatel
					<a
						itemprop="author"
						class="author"
						rel="author"
						href="<?= $this->link('Profile:view', ['username' => $recipe['user.username']]) ?>"
					><?= htmlspecialchars($recipe['user.name'] ?? $recipe['user.username']) ?></a>,
					<?= Helpers::timeEl($createdAt) ?>.
				</p>

				<div class="recipe-tools">
					<button type="button" class="btn btn-print">Tisknout recept</button>
				</div>

			<?php endif; ?>

			<section>
				<h2>Suroviny</h2>
				<div itemprop="ingredients">
					<?= Helpers::stringToHtml(($recipe['ingredients'] ?? '')) ?>
				</div>
			</section>

			<section>
				<h2>Postup</h2>
				<div itemprop="recipeInstructions">
					<?= Helpers::stringToHtml(($recipe['instructions'] ?? '')) ?>
				</div>
			</section>

		</div>
	</main>

	<?php require __DIR__ . '/_footer.php' ?>

</body>
