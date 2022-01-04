<?php

declare(strict_types=1);

namespace Core\Forms\Controls;

use Nette\Utils\Html;

/**
 * @phpstan-type HtmlLabel \Nette\HtmlStringable|string|int|float
 */
abstract class HtmlWithLabelControl extends HtmlControl
{

	protected Html $htmlLabelEl;

	/**
	 * @param string $name
	 * @param string $htmlElementName
	 * @param HtmlLabel $label
	 */
	public function __construct(string $name, string $htmlElementName, $label)
	{
		$this->htmlLabelEl = Html::el('label');
		$this->htmlLabelEl->setText($label);
		parent::__construct($name, $htmlElementName);
	}

	public function getLabel(): Html
	{
		return $this->htmlLabelEl;
	}

	protected function generateId(): void
	{
		parent::generateId();
		$this->htmlLabelEl->for = $this->htmlEl->id;
	}

}
