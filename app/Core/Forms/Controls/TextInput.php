<?php

declare(strict_types=1);

namespace Core\Forms\Controls;

class TextInput extends TextBaseControl
{

	public const
		TYPE_TEXT = 'text',
		TYPE_EMAIL = 'email',
		TYPE_PASSWORD = 'password';

	protected string $type = self::TYPE_TEXT;
	protected ?string $pattern = null;

	protected bool $outputPasswordValueEnabled = false;

	public function __construct(string $name, $label)
	{
		parent::__construct($name, 'input', $label);
		$this->defaultValidators = [
			'validateType',
			'validatePattern',
			'validateMinLength',
			'validateMaxLength',
		];
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
		if ($this->type !== self::TYPE_PASSWORD || $this->outputPasswordValueEnabled) {
			$this->htmlEl->value = $value;
		}
		return $this;
	}


	public function isOutputPasswordValueEnabled(): bool
	{
		return $this->outputPasswordValueEnabled;
	}

	public function setOutputPasswordValueEnabled(bool $outputPasswordValueEnabled): self
	{
		$this->outputPasswordValueEnabled = $outputPasswordValueEnabled;
		return $this;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function setType(string $type): self
	{
		$this->type = $type;
		$this->htmlEl->type = $type;
		return $this;
	}

	protected function validateType(): bool
	{
		// TODO: validate TYPE_EMAIL
		return true;
	}

	protected function validatePattern(): bool
	{
		// TODO: validate
		return true;
	}

}
