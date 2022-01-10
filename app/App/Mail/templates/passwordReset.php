<?php

declare(strict_types=1);

/**
 * @var Core\Config $config
 * @var Core\Routing\Router $router
 * @var string $key
 */

$subject = 'Obnovení hesla';

?>
Heslo si můžete resetovat pomocí následujícího odkazu (jehož platnost je omezena na 1 hodinu):
<?php $passwordResetLink = $router->fulllink('ResetPassword:', ['key' => $key]) ?>
<a href="<?= $passwordResetLink ?>">
	<?= $passwordResetLink ?>
</a>
