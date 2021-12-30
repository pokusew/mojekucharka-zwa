<?php

declare(strict_types=1);

namespace App\Template;

use Core\Forms\Controls\BaseControl;
use Core\Forms\Controls\TextBaseControl;
use Nette\Utils\Html;

class Helpers
{

	/**
	 * Renders the given form control to HTML
	 *
	 * Note! It also mutates the HTML elements attributes of the given control.
	 *
	 * @param TextBaseControl $control
	 * @return string HTML
	 */
	public static function renderFormControl(TextBaseControl $control): string
	{
		$group = Html::el('div');
		// @phpstan-ignore-next-line
		$group->class[] = 'form-group';

		$label = $control->getLabel()->class('form-control-label');

		$input = $control->getElem()->class('form-control');

		$group->insert(null, $label);
		$group->insert(null, $input);

		if ($control->hasError()) {

			$control->getElem()->data('touched', 'true');

			$feedback = Html::el('p');
			$feedback->class('form-control-feedback');
			$feedback->setText($control->getError());

			$group->class[] = 'has-error';
			$group->insert(null, $feedback);

		}

		return (string) $group;
	}

}
