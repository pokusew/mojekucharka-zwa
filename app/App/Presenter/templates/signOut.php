<?php

declare(strict_types=1);

/**
 * @var App\Presenter\SignOutPresenter $this
 * @var Core\Config $config
 * @var Core\Assets $assets
 * @var Core\Routing\Router $router
 */

$title = 'Odhlášení';

?>
<body class="app">

	<?php require __DIR__ . '/_header.php' ?>

	<main class="app-content">
		<div class="container">

			<h1>Odhlášení</h1>

			<?= $this->signOutForm->getElem()->startTag() ?>
			<?= $this->signOutForm['submit']->getElem()->class('btn btn-primary') ?>
			<?= $this->signOutForm->getElem()->endTag() ?>

		</div>
	</main>

	<?php require __DIR__ . '/_footer.php' ?>

</body>
