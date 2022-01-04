<?php

declare(strict_types=1);

/**
 * @var App\Presenter\SignUpPresenter $this
 * @var Core\Config $config
 * @var Core\Assets $assets
 * @var Core\Routing\Router $router
 */

use App\Helpers;

$title = 'Registrace';

?>
<body class="app">

	<?php require __DIR__ . '/_header.php' ?>

	<main class="app-content">
		<div class="container">

			<h1>Registrace</h1>

			<?= $this->signUpForm->getElem()->startTag() ?>

			<?= Helpers::renderFormControl($this->signUpForm['username']) ?>

			<?= Helpers::renderFormControl($this->signUpForm['email']) ?>

			<?= Helpers::renderFormControl($this->signUpForm['password']) ?>

			<?= Helpers::renderFormControl($this->signUpForm['passwordAgain']) ?>

			<?= $this->signUpForm['submit']->getElem()->class('btn btn-primary') ?>

			<?= $this->signUpForm->getElem()->endTag() ?>

		</div>
	</main>

	<?php require __DIR__ . '/_footer.php' ?>

</body>

