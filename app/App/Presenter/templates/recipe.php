<?php

declare(strict_types=1);

/**
 * @var App\Presenter\RecipePresenter $this
 * @var Core\Config $config
 * @var Core\Assets $assets
 * @var Core\Routing\Router $router
 */

use App\Helpers;

$title = 'Receipt';

?>
<body class="app">

	<?php require __DIR__ . '/_header.php' ?>

	<nav class="app-breadcrumbs breadcrumbs">
		<div class="container">
			<ol>
				<li><a href="<?= $this->link('Home:') ?>">Recepty</a></li>
				<li><a href="<?= $this->link('Home:') ?>">Cukroví</a></li>
				<li><a href="<?= $this->link('Home:') ?>">Domácí Oreo sušenky</a></li>
			</ol>
		</div>
	</nav>

	<main class="app-content">
		<div class="container">

			<h1><?= htmlspecialchars($this->recipe['name']) ?></h1>

			<span itemprop="recipeCategory"
				  class="category"><?= htmlspecialchars($this->recipe['category.name']) ?></span>

			<p class="muted">
				Přidal uživatel
				<a
					itemprop="author"
					class="author"
					rel="author"
					href="<?= $this->link('Profile:view', ['username' => $this->recipe['user.username']]) ?>"
				><?= htmlspecialchars($this->recipe['user.name'] ?? $this->recipe['user.username']) ?></a>,
				<time
					datetime="<?= Helpers::ds($this->recipe['created_at'])->format(DateTimeInterface::W3C) ?>"
				><?= Helpers::ds($this->recipe['created_at'])->format('j. n. Y \v H:i') ?></time>
			</p>

			<section>
				<h2>Suroviny</h2>
				<?= Helpers::stringToHtml(($this->recipe['ingredients'] ?? '')) ?>
			</section>

			<section>
				<h2>Postup</h2>
				<?= Helpers::stringToHtml(($this->recipe['instructions'] ?? '')) ?>
			</section>

			<hr />

			<button type="button" class="btn btn-print">Tisknout recept</button>

		</div>
	</main>

	<?php require __DIR__ . '/_footer.php' ?>

</body>
