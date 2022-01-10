<?php

declare(strict_types=1);

namespace Core\Forms\Controls;

/**
 * HTML button
 * @phpstan-import-type HtmlLabel from HtmlWithLabelControl
 */
class Button extends HtmlControl
{

	protected ?string $buttonValue = null;

	/**
	 * @param string $name
	 * @param HtmlLabel $label
	 * @param string|null $type
	 * @param string|null $value
	 */
	public function __construct(string $name, $label, ?string $type = 'submit', ?string $value = null)
	{
		parent::__construct($name, 'button');
		$this->htmlEl->setText($label);
		$this->htmlEl->type = $type;
		$this->setButtonValue($value);
	}

	/**
	 * @param mixed[] $data
	 * @return $this
	 */
	public function setValueFromRequest(array &$data): self
	{
		$htmlDataName = $this->htmlEl->name;
		$value = isset($data[$htmlDataName]) && is_string($data[$htmlDataName]) ? $data[$htmlDataName] : null;
		$this->setValue($value);
		return $this;
	}

	/**
	 * @param string|null $value
	 * @return $this
	 */
	public function setValue(?string $value): self
	{
		parent::setValue($value);
		$this->htmlEl->value = $this->buttonValue;
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

	/**
	 * @return string|null
	 */
	public function getButtonValue(): ?string
	{
		return $this->buttonValue;
	}

	/**
	 * @param string|null $buttonValue
	 * @return $this
	 */
	public function setButtonValue(?string $buttonValue): self
	{
		$this->buttonValue = $buttonValue;
		$this->htmlEl->value = $this->buttonValue;
		return $this;
	}

	public function wasUsedForSubmission(): bool
	{
		return $this->getValue() === $this->buttonValue;
	}

}
