<?php

declare(strict_types=1);

/**
 * @var App\Presenter\SignForgottenPresenter $this
 * @var Core\Config $config
 * @var Core\Assets $assets
 * @var Core\Routing\Router $router
 */

use App\Helpers;

$title = 'Zapomenut√© heslo';

?>
<body class="app">

	<?php require __DIR__ . '/_header.php' ?>

	<main class="app-content">
		<div class="container">

			<h1>Zapomenut√© heslo</h1>

			<?= $this->signForgottenForm->getElem()->startTag() ?>

			<?= Helpers::renderFormControl($this->signForgottenForm['email']) ?>

			<?= $this->signForgottenForm['submit']->getElem()->class('btn btn-primary') ?>

			<?= $this->signForgottenForm->getElem()->endTag() ?>

		</div>
	</main>

	<?php require __DIR__ . '/_footer.php' ?>

</body>
