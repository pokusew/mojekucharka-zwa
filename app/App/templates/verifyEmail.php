<?php

declare(strict_types=1);

/**
 * @var App\Presenter\VerifyEmailPresenter $this
 * @var Core\Config $config
 * @var Core\Assets $assets
 * @var Core\Routing\Router $router
 */

$title = 'Oveření e-mailové adresy';

?>
<body class="app">

	<?php require __DIR__ . '/_header.php' ?>

	<main class="app-content">
		<div class="container">

			<h1>Oveření e-mailové adresy</h1>

			<?php if ($this->success): ?>
				<p>
					Oveření e-mailové adresy bylo úspěšné.
					Nyní se můžete <a href="<?= $this->link('SignIn:') ?>">přihlásit</a>.
				</p>
			<?php else: ?>
				<p>
					Odkaz na oveření e-mailové adresy není platný.
				</p>
			<?php endif; ?>

		</div>
	</main>

	<?php require __DIR__ . '/_footer.php' ?>

</body>
