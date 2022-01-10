<?php

declare(strict_types=1);

namespace Core\Forms\Controls;

use Nette\Utils\Html;

/**
 * Base class for all form controls that have a HTML label element.
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

	/**
	 * @param HtmlLabel $label
	 * @return $this
	 */
	public function setLabelText($label): self
	{
		$this->htmlLabelEl->setText($label);
		return $this;
	}

	protected function generateId(): void
	{
		parent::generateId();
		$this->htmlLabelEl->for = $this->htmlEl->id;
	}

}
