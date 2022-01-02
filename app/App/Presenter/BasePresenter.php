<?php

declare(strict_types=1);

namespace App\Presenter;

use Core\UI\Presenter;

abstract class BasePresenter extends Presenter
{

	protected ?string $templatesDir = __DIR__ . '/../templates';
	protected ?string $layout = '_layout';

}
