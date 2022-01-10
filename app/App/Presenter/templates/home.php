<?php

declare(strict_types=1);

/**
 * @var App\Presenter\HomePresenter $this
 * @var Core\Config $config
 * @var Core\Assets $assets
 * @var Core\Routing\Router $router
 */

use App\RecipesFilter;

?>
<body class="app">

	<?php require __DIR__ . '/_header.php' ?>

	<main class="app-content">
		<div class="container">

			<h1>Moje kuchařka</h1>

			<p>
				Moje kuchařka je webová aplikace pro jednoduchou správu Vašich receptů.
				Umožňuje přidávat, upravovat a mazat recepty, přidávat obrázky k receptům,
				a tisknout je.
			</p>

			<p>
				Ve výchozím nastavení jsou všechny recepty soukromé, ale můžete je snadno zveřejnit.
			</p>

			<h2>Začněte ještě dnes</h2>

			<p>
				Jen pár kroků Vás dělí od používání Mojí kuchařky.
			</p>

			<ol>
				<li>
					<a href="<?= $this->link('SignUp:') ?>">Zaregistrujte se.</a>
					<p>
						Registrací získáte plný přístup k Mojí kuchařce.
						Přidávejte si vlastní recepty včetně obrázků, s možností dalších úprav či mazání.
					</p>

				</li>
				<li>
					<a href="<?= $this->link('SignIn:') ?>">Přihlaste se.</a>
				</li>
				<li>
					A využívejte Moji kuchařku naplno.
				</li>
			</ol>

			<p>
				Bez registrace a přihlášení si můžete prohlížet
				<a href="<?= $this->link(
					'Recipes:',
					null,
					false,
					RecipesFilter::DEFAULT_NOT_LOGGED_IN_QUERY,
				) ?>">veřejné recepty.</a>
			</p>

		</div>
	</main>

	<?php require __DIR__ . '/_footer.php' ?>

</body>
