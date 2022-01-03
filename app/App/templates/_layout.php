<?php

declare(strict_types=1);

/**
 * @var App\Presenter\BasePresenter $this
 * @var Core\Config $config
 * @var Core\Assets $assets
 * @var Core\Routing\Router $router
 * @var ?string $title
 * @var ?string $headers additional HTML to place in the HTML head
 * @var string $page current rendered page (HTML string)
 */

?>
<!DOCTYPE html>
<html lang="cs">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title><?= isset($title) ? htmlspecialchars($title) . ' | Mojekuchařka.net' : 'Mojekuchařka.net' ?></title>
		<link
			rel="manifest"
			href="<?= $assets->getUrl('manifest.json') ?>"
			integrity="<?= $assets->getIntegrity('manifest.json') ?>"
			crossorigin="anonymous"
		/>
		<?php if ($assets->isModeDevelopment()): ?>
			<script src="<?= $assets->webpackDevServerUrl . '/index.js' ?>"></script>
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
	<?= $page ?>
</html>
