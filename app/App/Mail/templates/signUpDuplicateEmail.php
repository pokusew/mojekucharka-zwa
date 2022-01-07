<?php

declare(strict_types=1);

/**
 * @var Core\Config $config
 * @var Core\Routing\Router $router
 * @var ?string $key
 */

$subject = 'Opakováná registrace';

?>
<p>
	Zaznamenali jsem pokus o registraci nového uživatelského účtu
	na webu <a href="<?= $router->fullLink('Home') ?>">Mojekuchařka.net</a> s Vaší e-mailovou adresou.
</p>

<p>
	<?php if ($key !== null): ?>
		Nicméně Vaše e-mailová adresa je již vázána k aktivnímu účtu. Přihlásit se můžete zde:
		<a href="<?= $router->fullLink('SignIn') ?>">
			<?= $router->fullLink('SignIn') ?>
		</a>
	<?php else: ?>
		Nicméně Vaše e-mailová adresa již byla použita pro registraci jiného účtu, který však ještě nebyl aktivován.
		<br />Registraci dokončíte pomocí následujícího odkazu:
		<?php $emailVerificationLink = $router->fullLink('VerifyEmail', ['key' => $key]) ?>
		<a href="<?= $emailVerificationLink ?>">
			<?= $emailVerificationLink ?>
		</a>
	<?php endif; ?>
</p>
