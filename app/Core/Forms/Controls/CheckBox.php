<?php

declare(strict_types=1);

namespace Core\Forms\Controls;

/**
 * HTML HTML input type=checkbox
 * @phpstan-import-type HtmlLabel from HtmlWithLabelControl
 */
class CheckBox extends HtmlWithLabelControl
{

	public const VALUE = 'true';

	/**
	 * @param string $name
	 * @param HtmlLabel $label
	 */
	public function __construct(string $name, $label)
	{
		parent::__construct($name, 'input', $label);
		$this->htmlEl->type = 'checkbox';
		$this->setValue(self::VALUE);
	}

	/**
	 * @param mixed[] $data
	 * @return $this
	 */
	public function setValueFromRequest(array &$data): self
	{
		$htmlDataName = $this->htmlEl->name;
		$value = isset($data[$htmlDataName]) && is_string($data[$htmlDataName]) ? $data[$htmlDataName] : null;
		$this->setChecked($value === self::VALUE);
		return $this;
	}

	/**
	 * @return $this
	 */
	public function setValue(?string $value): self
	{
		parent::setValue($value);
		$this->htmlEl->value = $value;
		return $this;
	}

	public function isChecked(): bool
	{
		return $this->htmlEl->checked === true;
	}

	/**
	 * @return $this
	 */
	public function setChecked(bool $checked): self
	{
		$this->htmlEl->checked = $checked;
		return $this;
	}

	protected function validateRequired(): bool
	{
		return $this->setErrorIf(
			$this->required && !$this->isChecked(),
			'Zaškrtněte prosím toto pole.'
		);
	}

}
