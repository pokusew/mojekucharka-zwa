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

	<nav class="app-breadcrumbs breadcrumbs">
		<div class="container">
			<ol>
				<li><a href="<?= $this->link('#') ?>">Recepty</a></li>
				<li><a href="<?= $this->link('#') ?>">Cukroví</a></li>
				<li><a href="<?= $this->link('#') ?>">Domácí Oreo sušenky</a></li>
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
					href="<?= $this->link('Profile', ['username' => 'Robot']) ?>"
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

	<?php require __DIR__ . '/_footer.php' ?>

</body>

