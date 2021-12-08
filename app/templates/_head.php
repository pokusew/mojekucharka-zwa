<?php
declare(strict_types=1);
/**
 * @var App\Config $config
 * @var App\Assets $assets
 * @var App\Router $router
 */
?>
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
