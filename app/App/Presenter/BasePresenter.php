<?php

namespace App\Presenter;

class BasePresenter extends \Core\UI\Presenter
{

	protected ?string $templatesDir = __DIR__ . '/../../templates';
	protected ?string $layout = '_layout';

}
