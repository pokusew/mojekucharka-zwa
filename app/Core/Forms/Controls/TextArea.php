<?php

declare(strict_types=1);

namespace Core\Forms\Controls;

class TextArea extends HtmlWithLabelControl
{

	public function __construct(string $name, $label)
	{
		parent::__construct($name, 'textarea', $label);
	}

	public function setValueFromRequest(array &$data): self
	{
		$htmlDataName = $this->htmlEl->name;
		$value = isset($data[$htmlDataName]) && is_string($data[$htmlDataName]) ? $data[$htmlDataName] : null;
		$this->setValue($value);
		return $this;
	}

	public function setValue(?string $value): HtmlControl
	{
		parent::setValue($value);
		$this->htmlEl->setText($value);
		return $this;
	}

}
