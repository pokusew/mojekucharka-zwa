<?php

declare(strict_types=1);

namespace Core\Forms\Controls;

class TextInput extends TextBase
{

	public const
		TYPE_TEXT = 'text',
		TYPE_EMAIL = 'email',
		TYPE_PASSWORD = 'password';

	protected string $type = self::TYPE_TEXT;
	protected ?string $pattern = null;

	public function __construct(string $name, string $label)
	{
		parent::__construct($name, $label);
		$this->defaultValidators = [
			'validateType',
			'validatePattern',
			'validateMinLength',
			'validateMaxLength',
		];
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
