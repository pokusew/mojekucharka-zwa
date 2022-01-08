<?php

declare(strict_types=1);

/**
 * @var App\Presenter\BasePresenter $this
 * @var Core\Config $config
 * @var Core\Assets $assets
 * @var Core\Routing\Router $router
 * @var ?string $title
 * @var ?string $headers additional HTML to place in the HTML head
 */

use Core\Template\Html;

?>
<header class="app-header">
	<div class="container">

		<input id="app-navigation-toggle" type="checkbox">
		<label id="app-navigation-toggle-label" for="app-navigation-toggle">Menu</label>

		<svg class="app-logo mojekucharka-logo" viewBox="0 0 230 230">
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

		<a class="app-name" href="<?= $this->link('Home:') ?>">Mojekuchařka.net</a>

		<nav class="app-navigation">
			<ul class="left">
				<li>
					<a
						<?= Html::attrClass(['active' => $this->isLinkCurrent('Home:')]) ?>
						href="<?= $this->link('Home:') ?>"
					>
						Úvod
					</a>
				</li>
				<li>
					<a
						<?= Html::attrClass(['active' => $this->isLinkCurrent('Recipes:')]) ?>
						href="<?= $this->link('Recipes:') ?>"
					>
						Recepty
					</a>
				</li>
			</ul>
			<ul class="right">
				<li>
					<a
						<?= Html::attrClass(['active' => $this->isLinkCurrent('SignIn:')]) ?>
						href="<?= $this->link('SignIn:') ?>"
					>
						Přihlášení
					</a>
				</li>
				<li>
					<a
						<?= Html::attrClass(['active' => $this->isLinkCurrent('SignUp:')]) ?>
						href="<?= $this->link('SignUp:') ?>"
					>
						Registrace
					</a>
				</li>
			</ul>
		</nav>

	</div>
</header>
