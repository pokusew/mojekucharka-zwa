<?php

declare(strict_types=1);

/**
 * @var Core\Config $config
 * @var Core\Routing\Router $router
 * @var string $key
 */

$subject = 'Dokončení registrace';

?>
Registraci dokončíte pomocí následujícího odkazu:
<?php $emailVerificationLink = $router->fullLink('VerifyEmail', ['key' => $key]) ?>
<a href="<?= $emailVerificationLink ?>">
	<?= $emailVerificationLink ?>
</a>


