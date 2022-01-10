<?php

declare(strict_types=1);

/**
 * @var App\Presenter\SettingsPresenter $this
 * @var Core\Config $config
 * @var Core\Assets $assets
 * @var Core\Routing\Router $router
 */

$title = 'Nastavení';

?>
<body class="app">

	<?php require __DIR__ . '/_header.php' ?>

	<main class="app-content">
		<div class="container">

			<h1>Nastavení</h1>

			<?= $this->signOutForm->getElem()->startTag() ?>
			<?= $this->signOutForm['submit']->getElem()->class('btn btn-primary') ?>
			<?= $this->signOutForm->getElem()->endTag() ?>

			<br />

			<a class="btn" href="<?= $this->link('Settings:changePassword') ?>">
				Změnit heslo
			</a>

			<a class="btn" href="<?= $this->link('Settings:editProfile') ?>">
				Upravit profil
			</a>

		</div>
	</main>

	<?php require __DIR__ . '/_footer.php' ?>

</body>
