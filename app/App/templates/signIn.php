<?php

declare(strict_types=1);

/**
 * @var App\Presenter\SignInPresenter $this
 * @var Core\Config $config
 * @var Core\Assets $assets
 * @var Core\Routing\Router $router
 */

use App\Helpers;

?>
<body class="app">

	<?php require __DIR__ . '/_header.php' ?>

	<main class="app-content">
		<div class="container">

			<h1>Přihlášení</h1>

			<?= $this->signInForm->getElem()->startTag() ?>

			<?= Helpers::renderFormControl($this->signInForm['email']) ?>

			<?= Helpers::renderFormControl($this->signInForm['password']) ?>

			<?= $this->signInForm['submit']->getElem()->class('btn btn-primary') ?>

			<?= $this->signInForm->getElem()->endTag() ?>

		</div>
	</main>

	<?php require __DIR__ . '/_footer.php' ?>

</body>

