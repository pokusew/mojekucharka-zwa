<?php

declare(strict_types=1);

/**
 * @var App\Presenter\SignForgottenPresenter $this
 * @var Core\Config $config
 * @var Core\Assets $assets
 * @var Core\Routing\Router $router
 */

$title = 'Zapomenuté heslo';

?>
<body class="app">

	<?php require __DIR__ . '/_header.php' ?>

	<main class="app-content">
		<div class="container">

			<h1>Zapomenuté heslo</h1>

			<p>
				Pokud jste zadali e-mailovou adresu, která je navázaná na existující účet, tak jsme Vám zaslali
				právě odeslali odkaz pro resetování hesla na tuto adresu.
			</p>

		</div>
	</main>

	<?php require __DIR__ . '/_footer.php' ?>

</body>
