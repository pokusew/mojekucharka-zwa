<?php

declare(strict_types=1);

namespace Core\Forms\Controls;

/**
 * HTML button
 * @phpstan-import-type HtmlLabel from HtmlWithLabelControl
 */
class Button extends HtmlControl
{

	/**
	 * @param string $name
	 * @param HtmlLabel $label
	 * @param string|null $type
	 */
	public function __construct(string $name, $label, ?string $type = 'submit')
	{
		parent::__construct($name, 'button');
		$this->htmlEl->setText($label);
		$this->htmlEl->type = $type;
	}

	/**
	 * @param mixed[] $data
	 * @return $this
	 */
	public function setValueFromRequest(array &$data): self
	{
		// TODO
		return $this;
	}

	public function getType(): ?string
	{
		return $this->htmlEl->type;
	}

	/**
	 * @return $this
	 */
	public function setType(?string $type): self
	{
		$this->htmlEl->type = $type;
		return $this;
	}

}
