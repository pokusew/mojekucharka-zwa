<?php

declare(strict_types=1);

namespace Core\Forms\Controls;

use Nette\Utils\Html;

abstract class HtmlWithLabelControl extends HtmlControl
{

	protected Html $htmlLabelEl;

	public function __construct(string $name, string $htmlElementName, $label)
	{
		$this->htmlLabelEl = Html::el('label');
		$this->htmlLabelEl->setText($label);
		parent::__construct($name, $htmlElementName);
		$this->defaultValidators[] = 'validateMinLength';
		$this->defaultValidators[] = 'validateMaxLength';
	}

	public function getLabel(): Html
	{
		return $this->htmlLabelEl;
	}

	protected function generateId()
	{
		parent::generateId();
		$this->htmlLabelEl->for = $this->htmlEl->id;
	}

}
