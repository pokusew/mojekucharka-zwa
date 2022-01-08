<?php

declare(strict_types=1);

/**
 * @var App\Presenter\ProfilePresenter $this
 * @var Core\Config $config
 * @var Core\Assets $assets
 * @var Core\Routing\Router $router
 */

$title = 'Uživatel TODO';

?>
<body class="app">

	<?php require __DIR__ . '/_header.php' ?>

	<main class="app-content">
		<div class="container">

			<h1>Uživatel <?= htmlspecialchars($this->user['username']) ?></h1>

		</div>
	</main>

	<?php require __DIR__ . '/_footer.php' ?>

</body>
