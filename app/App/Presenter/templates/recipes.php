<?php

declare(strict_types=1);

/**
 * @var App\Presenter\RecipesPresenter $this
 * @var Core\Config $config
 * @var Core\Assets $assets
 * @var Core\Routing\Router $router
 */

use App\Helpers;

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

			<h1>Recepty</h1>

			<?php if ($this->isUserLoggedIn()): ?>
				<a class="btn btn-primary" href="<?= $this->link('Recipe:new') ?>">Nový recept</a>
			<?php endif; ?>

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

			<?= $pagination ?>

			<p>
				<?= $this->paginator->getNumberOfFirstItemOnPage() ?>.
				až <?= $this->paginator->getNumberOfLastItemOnPage() ?>.
				z <?= $this->paginator->getItemsCount() ?>
			</p>

			<div class="recipes">


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

		</div>
	</main>

	<?php require __DIR__ . '/_footer.php' ?>

</body>
