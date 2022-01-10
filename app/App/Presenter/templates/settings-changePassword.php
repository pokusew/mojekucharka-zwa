<?php

declare(strict_types=1);

/**
 * @var App\Presenter\SettingsPresenter $this
 * @var Core\Config $config
 * @var Core\Assets $assets
 * @var Core\Routing\Router $router
 */

use App\Helpers;

$title = 'Změna hesla';

?>
<body class="app">

	<?php require __DIR__ . '/_header.php' ?>

	<main class="app-content">
		<div class="container">

			<h1>Změna hesla</h1>

			<?= $this->changePasswordForm->getElem()->startTag() ?>

			<?php
			// Password forms should have (optionally hidden) username fields for accessibility:
			// (More info: https://goo.gl/9p2vKq)
			?>
			<!--suppress HtmlFormInputWithoutLabel -->
			<input
				aria-hidden="true"
				type="text"
				hidden="hidden"
				name="username"
				autocomplete="username"
				value="<?= $this->getUser()->getUsername() ?>"
			/>

			<?= Helpers::renderFormControl($this->changePasswordForm['currentPassword']) ?>

			<?= Helpers::renderFormControl($this->changePasswordForm['password']) ?>

			<?= Helpers::renderFormControl($this->changePasswordForm['passwordAgain']) ?>

			<?= $this->changePasswordForm['submit']->getElem()->class('btn btn-primary') ?>

			<?= $this->changePasswordForm->getElem()->endTag() ?>

		</div>
	</main>

	<?php require __DIR__ . '/_footer.php' ?>

</body>
