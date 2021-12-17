<?php

declare(strict_types=1);

namespace Core\Forms\Controls;

class Button extends HtmlControl
{

	public function __construct(string $name, $label, ?string $type = 'submit')
	{
		parent::__construct($name, 'button');
		$this->htmlEl->setText($label);
		$this->htmlEl->type = $type;
	}

	public function getType(): ?string
	{
		return $this->htmlEl->type;
	}

	public function setType(?string $type): self
	{
		$this->htmlEl->type = $type;
		return $this;
	}

}
