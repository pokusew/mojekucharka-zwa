<?php

declare(strict_types=1);

/**
 * @var App\Presenter\HomePresenter $this
 * @var Core\Config $config
 * @var Core\Assets $assets
 * @var Core\Routing\Router $router
 */

?>
<body class="app">

	<?php require __DIR__ . '/_header.php' ?>

	<main class="app-content">
		<div class="container">

			<h1>Přihlášení</h1>

			<form
				name="signIn"
				method="post"
				action=""
			>

				<label for="signIn--email">
					E-mail
				</label>

				<input
					id="signIn--email"
					name="email"
					type="email"
				/>

				<button type="submit" class="btn btn-primary">Submit</button>

			</form>

		</div>
	</main>

	<?php require __DIR__ . '/_footer.php' ?>

</body>

