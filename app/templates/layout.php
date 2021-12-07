<?php
declare(strict_types=1);
/**
 * @var App\Config $config
 * @var App\Assets $assets
 * @var App\Router $router
 */
?>
<!DOCTYPE html>
<html lang="cs">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>Mojekuchařka.net</title>
		<link
			rel="manifest"
			href="<?= $assets->getUrl('manifest.json') ?>"
			integrity="<?= $assets->getIntegrity('manifest.json') ?>"
			crossorigin="anonymous"
		/>
		<?php if ($config->isDevelopment()): ?>
			<script src="<?= $config->webpackDevServer . '/index.js' ?>"></script>
		<?php else: ?>
			<link
				rel="stylesheet"
				href="<?= $assets->getUrl('index.css') ?>"
				integrity="<?= $assets->getIntegrity('index.css') ?>"
				crossorigin="anonymous"
			/>
			<script
				defer="defer"
				src="<?= $assets->getUrl('index.js') ?>"
				integrity="<?= $assets->getIntegrity('index.js') ?>"
				crossorigin="anonymous"
			></script>
		<?php endif; ?>
	</head>
	<body class="app">
		<header class="app-header">
			<div class="container">
				<input id="app-navigation-toggle" type="checkbox">
				<label id="app-navigation-toggle-label" for="app-navigation-toggle">Menu</label>

				<svg class="app-logo" viewBox="0 0 230 230">
					<circle
						class="plate"
						cx="115" cy="115" r="91"
					/>
					<path
						class="spoon"
						d="M152.184,212.113c0,10.516 17.632,10.516 17.632,0l-3.526,-123.568c0,-7.888 10.579,-10.517 17.632,-21.033c10.58,-15.775 -1.763,-46.01 -10.579,-52.582c-8.817,-6.573 -15.869,-6.573 -24.686,0c-8.816,6.572 -21.159,36.807 -10.579,52.582c7.053,10.516 17.632,13.145 17.632,21.033l-3.526,123.568Z"
					/>
					<path
						class="fork"
						d="M77.385,212.234c0,10.355 -15.77,10.355 -15.77,0l3.154,-119.075c0,-7.766 -15.769,-7.766 -15.769,-18.12l1.577,-62.127c0,-3.883 4.731,-3.883 4.731,0l0,51.772c0,5.177 6.307,5.177 6.307,0l0,-51.772c0,-3.883 4.731,-3.883 4.731,0l0,55.655c0,5.177 6.308,5.177 6.308,0l0,-55.655c0,-3.883 4.731,-3.883 4.731,0l0,51.772c0,5.177 6.307,5.177 6.307,0l0,-51.772c0,-3.883 4.731,-3.883 4.731,0l1.577,62.127c0,10.354 -15.769,10.354 -15.769,18.12l3.154,119.075l0,0Z"
					/>
				</svg>


				<a class="app-name" href="<?= $router->getUrl('/') ?>">Mojekuchařka.net</a>
				<nav class="app-navigation">
					<ul class="left">
						<li><a class="active" href="<?= $router->getUrl('/') ?>">Úvod</a></li>
						<li><a class="" href="<?= $router->getUrl('/recepty') ?>">Recepty</a></li>
					</ul>
					<ul class="right">
						<li><a class="" href="<?= $router->getUrl('/prihlaseni') ?>">Přihlášení</a></li>
						<li><a class="" href="<?= $router->getUrl('/registrace') ?>">Registrace</a></li>
					</ul>
				</nav>

			</div>
		</header>

		<nav class="app-breadcrumbs breadcrumbs">
			<div class="container">
				<ol>
					<li><a href="<?= $router->getUrl('#') ?>">Recepty</a></li>
					<li><a href="<?= $router->getUrl('#') ?>">Cukroví</a></li>
					<li><a href="<?= $router->getUrl('#') ?>">Domácí Oreo sušenky</a></li>
				</ol>
			</div>
		</nav>
		<main class="app-content">
			<div class="container">

				<h1>Domácí Oreo sušenky</h1>

				<p class="muted">
					Přidal uživatel
					<a
						itemprop="author"
						class="author"
						rel="author"
						href="<?= $router->getUrl('/profil/Robot') ?>"
					>Robot</a>,
					<time datetime="2013-07-09T15:03+02:00">09. 07. 2013 v 15:03</time>
				</p>

				<section>
					<h2>Suroviny</h2>

					<strong>Na těsto potřebujeme:</strong>

					<br />300 g hladké mouky
					<br />180 g tuku
					<br />100 g moučkového cukru
					<br />1 celé vejce
					<br />5 polévkových lžic kakaa
					<br />1/2 balíčku prášku do pečiva

					<br /><strong>Na náplň potřebujeme:</strong>

					<br />1 vaničku tvarohu
					<br />asi 100 g sušeného mléka
					<br />moučkový cukr dle potřeby

				</section>

				<section>

					<h2>Postup</h2>

					<p>
						Všechny suroviny smícháme dohromady a vytvoříme těsto, které necháme v chladu odpočinout. Z těsta vyválíme silnější placku, z které kulatým vykrajovátkem vykrajujeme kolečka. Kolečka pečeme ve vyhřáté troubě asi tak 15 minut.
					</p>

					<p>
						Upečená kolečka slepujeme náplní připravenou z tvarohu a sušeného mléka. do tvarohu postupně přidáváme sušené mléko tak, aby nám vznikl hustší krém. Podle chuti dosladíme.
						A teď už jen nanést na kolečka, přiklopit a nechat chvilku zatuhnout!
					</p>

					<p>
						Recept převzat z blogu Jedlíkovo vaření.
					</p>

				</section>

				<hr />

				<button type="button" class="btn btn-print">Tisknout recept</button>

			</div>
		</main>
		<footer class="app-footer">
			Mojekuchařka.net – místo pro vaše recepty
			<p>&copy; 2021 Martin Endler</p>
		</footer>
	</body>
</html>
