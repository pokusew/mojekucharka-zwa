<?php

declare(strict_types=1);

/**
 * @var App\Presenter\ProfilePresenter $this
 * @var Core\Config $config
 * @var Core\Assets $assets
 * @var Core\Routing\Router $router
 */

use App\Helpers;

$displayName = $this->user['name'] ?? $this->user['username'];
$registeredAt = Helpers::ds($this->user['registered_at']);

$title = "Uživatel $displayName";

?>
<body class="app">

	<?php require __DIR__ . '/_header.php' ?>

	<main class="app-content">
		<div class="container">

			<h1><?= htmlspecialchars($title) ?></h1>

			<p>
				Datum registrace: <?= Helpers::timeEl($registeredAt) ?>
			</p>

		</div>
	</main>

	<?php require __DIR__ . '/_footer.php' ?>

</body>
