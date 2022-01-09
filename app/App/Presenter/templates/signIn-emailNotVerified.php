<?php

declare(strict_types=1);

/**
 * @var App\Presenter\SignInPresenter $this
 * @var Core\Config $config
 * @var Core\Assets $assets
 * @var Core\Routing\Router $router
 */

$title = 'Přihlášení';

?>
<body class="app">

	<?php require __DIR__ . '/_header.php' ?>

	<main class="app-content">
		<div class="container">

			<h1>Účet není oveřený</h1>

			<p>
				Abyste se mohli přihlásit do Vašeho účtu, je potřeba nejdříve dokončit registraci pomocí odkazu,
				který jsme Vám zaslali na Vaši e-mailovou adresu.
			</p>

			<p>
				Žádný e-mail Vám nedorazil? Zkontrolujte prosím i složku se spamem. Pořád nic?
				Zkuste se znovu zaregistrovat.
			</p>

		</div>
	</main>

	<?php require __DIR__ . '/_footer.php' ?>

</body>
