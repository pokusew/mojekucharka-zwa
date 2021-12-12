<?php

declare(strict_types=1);

namespace App\Template;

use Core\Forms\Controls\TextBase;
use Nette\Utils\Html;

class Helpers
{

	public static function renderFormControl(TextBase $control): string
	{
		$group = Html::el('div');
		$group->class('form-group');

		$label = $control->getLabel()->class('form-control-label');

		$input = $control->getElem()->class('form-control');

		$group->insert(null, $label);
		$group->insert(null, $input);

		if ($control->hasError()) {

			$feedback = Html::el('p');
			$feedback->class('form-control-feedback');
			$feedback->setText($control->getError());

			$group->class('has-error');
			$group->insert(null, $feedback);

		}

		return (string) $group;
	}

}
