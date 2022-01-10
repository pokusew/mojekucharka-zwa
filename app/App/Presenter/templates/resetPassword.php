<?php

declare(strict_types=1);

/**
 * @var App\Presenter\ResetPasswordPresenter $this
 * @var Core\Config $config
 * @var Core\Assets $assets
 * @var Core\Routing\Router $router
 */

use App\Helpers;

$title = 'Resetování hesla';

$username = $this->user !== null ? $this->user['username'] : null;

?>
<body class="app">

	<?php require __DIR__ . '/_header.php' ?>

	<main class="app-content">
		<div class="container">

			<?php if ($username !== null): ?>

				<h1>Nastavte si nové heslo</h1>

				<?= $this->resetPasswordForm->getElem()->startTag() ?>

				<?= Helpers::renderFormError($this->resetPasswordForm) ?>

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
					value="<?= $username ?>"
				/>

				<?= Helpers::renderFormControl($this->resetPasswordForm['password']) ?>

				<?= Helpers::renderFormControl($this->resetPasswordForm['passwordAgain']) ?>

				<?= $this->resetPasswordForm['submit']->getElem()->class('btn btn-primary') ?>

				<?= $this->resetPasswordForm->getElem()->endTag() ?>

			<?php else: ?>

				<h1>Resetování hesla</h1>

				<p>
					Odkaz na resetování hesla <strong>není platný, vypršela jeho platnost,
						nebo byl mezitím vygenerován jiný (novejší) odkaz</strong>.
				</p>

				<p>
					Nový si můžete nechat zaslat pomocí
					<a href="<?= $this->link('SignForgotten:') ?>">formuláře na zapometuné heslo.</a>
				</p>

			<?php endif; ?>

		</div>
	</main>

	<?php require __DIR__ . '/_footer.php' ?>

</body>
