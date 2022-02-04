<?php

declare(strict_types=1);

/**
 * @var App\Presenter\RecipesPresenter $this
 * @var Core\Config $config
 * @var Core\Assets $assets
 * @var Core\Routing\Router $router
 */

use App\Helpers;
use App\Icons;
use App\RecipesFilter;
use Core\Database\SqlBuilder;
use Core\Template\Html;

$title = 'Recepty';

$pagination = Helpers::renderPagination(
	$this->paginator,
	function (int $pageNumber): string {
		return $this->recipesLink(['page' => $pageNumber]);
	},
	'Stránkování receptů',
);

$getCategoryName = function (int $id) {
	if (!isset($this->categories['map'][$id])) {
		return 'Neznámá kategorie';
	}
	return $this->categories['map'][$id]['name'];
};

$userId = $this->isUserLoggedIn() ? $this->getUser()->getId() : null;

?>
<body class="app">

	<?php require __DIR__ . '/_header.php' ?>

	<main class="app-content">
		<div class="container">

			<div class="recipes-view">
				<div class="recipes-categories">
					<ul>
						<?php foreach ($this->categories['nested'] as $topLevelCategory): ?>
							<li>
								<?= htmlspecialchars($topLevelCategory['name']) ?>
								<?php if (count($topLevelCategory['children']) > 0): ?>
									<ul>
										<?php foreach ($topLevelCategory['children'] as $category): ?>
											<li>
												<a href="<?= $this->recipesLink(['category' => $category['id']]) ?>">
													<?= htmlspecialchars($category['name']) ?>
												</a>
											</li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div class="recipes-list">

					<h1>Recepty</h1>

					<?php if ($this->isUserLoggedIn()): ?>
						<a
							class="btn btn-warning btn-new-recipe"
							href="<?= $this->link('Recipe:new') ?>"
						>Nový recept</a>
					<?php endif; ?>

					<div class="recipes-filter">

						<?php if ($this->filter->getCategory() !== null): ?>
							<div class="recipes-filter-categories">
								Pouze kategorie:
								<div class="pills">
									<div class="pill">
										<span
											class="pill-label"
										><?= htmlspecialchars($this->filter->getCategory()['name']) ?></span>
										<a
											href="<?= $this->recipesLink(['category' => null]) ?>"
											class="pill-close"
											aria-label="Odstranit kategorii z výběru"
										>
											<?= Icons::FA_TIMES_REGULAR ?>
										</a>
									</div>
								</div>
							</div>
						<?php endif; ?>

						<?php if ($this->isUserLoggedIn()): ?>
							<div class="recipes-filter-public">

								Filtrovat podle vlastníka:

								<div class="toggle-group">
									<a
										href="<?= $this->recipesLink(['owner' => RecipesFilter::OWNER_ME]) ?>"
										<?= Html::attrClass('toggle', ['active' => $this->filter->getOwner() === RecipesFilter::OWNER_ME]) ?>
									>
										<?= Icons::FA_LOCK_DUOTONE ?>
										<span>Pouze moje recepty</span>
									</a>
									<a
										href="<?= $this->recipesLink(['owner' => RecipesFilter::OWNER_OTHERS]) ?>"
										<?= Html::attrClass('toggle', ['active' => $this->filter->getOwner() === RecipesFilter::OWNER_OTHERS]) ?>
									>
										<?= Icons::FA_GLOBAL_EUROPA_DUOTONE ?>
										<span>Pouze recepty ostatních uživatelů</span>
									</a>
									<a
										href="<?= $this->recipesLink(['owner' => RecipesFilter::OWNER_ALL]) ?>"
										<?= Html::attrClass('toggle', ['active' => $this->filter->getOwner() === RecipesFilter::OWNER_ALL]) ?>
									>
										<span>Všechny recepty</span>
									</a>
								</div>

							</div>
						<?php endif; ?>


						<div class="recipes-ordering">

							Řadit podle:

							<div class="toggle-group">
								<a
									href="<?= $this->recipesLink(['sort' => RecipesFilter::SORT_NAME]) ?>"
									<?= Html::attrClass('toggle', ['active' => $this->filter->getSort() === RecipesFilter::SORT_NAME]) ?>
								>
									<?= Icons::FA_TAG_DUOTONE ?>
									<span>Názvu</span>
								</a>
								<a
									href="<?= $this->recipesLink(['sort' => RecipesFilter::SORT_USER]) ?>"
									<?= Html::attrClass('toggle', ['active' => $this->filter->getSort() === RecipesFilter::SORT_USER]) ?>
								>
									<?= Icons::FA_USER_DUOTONE ?>
									<span>Autora</span>
								</a>
								<a
									href="<?= $this->recipesLink(['sort' => RecipesFilter::SORT_CREATED]) ?>"
									<?= Html::attrClass('toggle', ['active' => $this->filter->getSort() === RecipesFilter::SORT_CREATED]) ?>
								>
									<?= Icons::FA_CLOCK_DUOTONE ?>
									<span>Data přidání</span>
								</a>
								<a
									href="<?= $this->recipesLink(['sort' => RecipesFilter::SORT_CHANGED]) ?>"
									<?= Html::attrClass('toggle', ['active' => $this->filter->getSort() === RecipesFilter::SORT_CHANGED]) ?>
								>
									<?= Icons::FA_CLOCK_DUOTONE ?>
									<span>Data poslední změny</span>
								</a>
							</div>

							<div class="toggle-group">
								<a
									aria-label="vzestupně (A-Z)"
									href="<?= $this->recipesLink(['order' => SqlBuilder::ORDER_ASC]) ?>"
									<?= Html::attrClass('toggle', ['active' => $this->filter->getOrder() === SqlBuilder::ORDER_ASC]) ?>
								>
									<?= Icons::FA_SORT_ALPHA_UP_DUOTONE ?>
								</a>
								<a
									aria-label="sestupně (Z-A)"
									href="<?= $this->recipesLink(['order' => SqlBuilder::ORDER_DESC]) ?>"
									<?= Html::attrClass('toggle', ['active' => $this->filter->getOrder() === SqlBuilder::ORDER_DESC]) ?>
								>
									<?= Icons::FA_SORT_ALPHA_DOWN_ALT_DUOTONE ?>
								</a>
							</div>

						</div>

						<div class="pagination-info">
							Zobrazuji
							<?= $this->paginator->getNumberOfFirstItemOnPage() ?>.
							až <?= $this->paginator->getNumberOfLastItemOnPage() ?>.
							z <?= $this->paginator->getItemsCount() ?>
						</div>

					</div>

					<div class="recipes-list">
						<?php foreach ($this->recipes as $recipe): ?>
							<div class="recipe">
								<div class="recipe-image">

								</div>
								<div class="recipe-details">
									<a href="<?= $this->link('Recipe:view', ['id' => $recipe['id']]) ?>">
										<h3><?= htmlspecialchars($recipe['name']) ?></h3>
									</a>
									<div class="recipe-category">
										<?= htmlspecialchars($getCategoryName($recipe['category_id'])) ?>
									</div>
									<div class="recipe-author">
										<span class="label">Autor:</span>
										<a
											href="<?= $this->link('Profile:view', ['username' => $recipe['user.username']]) ?>"
										><?= htmlspecialchars($recipe['user.name'] ?? $recipe['user.username']) ?></a>
									</div>
									<div class="recipe-dates">
										Přidáno <?= Helpers::timeEl(Helpers::ds($recipe['created_at'])) ?>.
										<?php if (isset($recipe['changed_at'])): ?>
											<br />Naposledy upraveno <?= Helpers::timeEl(Helpers::ds($recipe['changed_at'])) ?>.
										<?php endif; ?>
									</div>
									<?php if ($userId !== null && $userId === $recipe['user_id']): ?>
										<div <?= Html::attrClass('recipe-visibility', ['public' => (bool) $recipe['public']]) ?>>
											<span class="label">Veřejný:</span>
											<?= $recipe['public'] ? Icons::FA_GLOBAL_EUROPA_DUOTONE : Icons::FA_LOCK_DUOTONE ?>
											<span class="value"><?= $recipe['public'] ? 'ano' : 'ne' ?></span>
										</div>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>

					<?= $pagination ?>

					<div class="pagination-info">
						Zobrazuji
						<?= $this->paginator->getNumberOfFirstItemOnPage() ?>.
						až <?= $this->paginator->getNumberOfLastItemOnPage() ?>.
						z <?= $this->paginator->getItemsCount() ?>
					</div>

				</div>
			</div>

		</div>
	</main>

	<?php require __DIR__ . '/_footer.php' ?>

</body>
