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

$title = 'Recepty';

$pagination = Helpers::renderPagination(
	$this->paginator,
	function (int $pageNumber): string {
		return $this->link('this');
	},
	'Stránkování receptů',
);

$getCategoryName = function (int $id) {
	if (!isset($this->categories['map'][$id])) {
		return 'Neznámá ktegorie';
	}
	return $this->categories['map'][$id]['name'];
}

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
								<a href="<?= $this->link('this', ['category' => $topLevelCategory['id']]) ?>">
									<?= htmlspecialchars($topLevelCategory['name']) ?>
								</a>
								<?php if (count($topLevelCategory['children']) > 0): ?>
									<ul>
										<?php foreach ($topLevelCategory['children'] as $category): ?>
											<li>
												<a href="<?= $this->link('this', ['category' => $category['id']]) ?>">
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
						<a class="btn btn-warning btn-new-recipe" href="<?= $this->link('Recipe:new') ?>">Nový recept</a>
					<?php endif; ?>

					<div class="recipes-filter">

						<div class="recipes-filter-categories">
							Pouze kategorie:
							<div class="pills">
								<div class="pill">
									<span class="pill-label">Bábovky</span>
									<a href="#" class="pill-close" aria-label="Odstranit kategorii z výběru">
										<?= Icons::FA_TIMES_REGULAR ?>
									</a>
								</div>
							</div>

						</div>

						<div class="recipes-filter-public">

							Filtrovat podle viditelnosti:

							<div class="toggle-group">
								<a href="#" class="toggle active" aria-current="page">
									<?= Icons::FA_LOCK_DUOTONE ?>
									<span>Pouze moje recepty</span>
								</a>
								<a href="#" class="toggle">
									<?= Icons::FA_GLOBAL_EUROPA_DUOTONE ?>
									<span>Pouze veřejné recepty</span>
								</a>
								<a href="#" class="toggle">
									<span>Všechny recepty</span>
								</a>
							</div>

						</div>

						<div class="recipes-ordering">

							Řadit podle:

							<div class="toggle-group">
								<a href="#" class="toggle active">
									<?= Icons::FA_TAG_DUOTONE ?>
									<span>Názvu</span>
								</a>
								<a href="#" class="toggle">
									<?= Icons::FA_USER_DUOTONE ?>
									<span>Autora</span>
								</a>
								<a href="#" class="toggle">
									<?= Icons::FA_CLOCK_DUOTONE ?>
									<span>Data přidání</span>
								</a>
								<a href="#" class="toggle">
									<?= Icons::FA_CLOCK_DUOTONE ?>
									<span>Data poslední změny</span>
								</a>
							</div>

							<div class="toggle-group">
								<a href="#" class="toggle active" aria-label="vzestupně (A-Z)">
									<?= Icons::FA_SORT_ALPHA_UP_DUOTONE ?>
								</a>
								<a href="#" class="toggle" aria-label="sestupně (Z-A)">
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
									<a
										class="recipe-author"
										href="<?= $this->link('Profile:view', ['username' => $recipe['user.username']]) ?>"
									><?= htmlspecialchars($recipe['user.name'] ?? $recipe['user.username']) ?></a>

								</div>
							</div>
						<?php endforeach; ?>
					</div>

					<?= $pagination ?>

				</div>
			</div>

		</div>
	</main>

	<?php require __DIR__ . '/_footer.php' ?>

</body>
