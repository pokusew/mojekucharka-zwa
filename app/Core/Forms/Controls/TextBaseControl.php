<?php

declare(strict_types=1);

namespace Core\Forms\Controls;

/**
 * @phpstan-import-type HtmlLabel from HtmlWithLabelControl
 */
abstract class TextBaseControl extends HtmlWithLabelControl
{

	protected ?int $minLength = null;
	protected ?int $maxLength = null;

	/**
	 * @param string $name
	 * @param string $htmlElementName
	 * @param HtmlLabel $label
	 */
	public function __construct(string $name, string $htmlElementName, $label)
	{
		parent::__construct($name, $htmlElementName, $label);
		$this->defaultValidators[] = 'validateMinLength';
		$this->defaultValidators[] = 'validateMaxLength';
	}

	public function getMinLength(): ?int
	{
		return $this->minLength;
	}

	/**
	 * @return $this
	 */
	public function setMinLength(?int $minLength): self
	{
		$this->minLength = $minLength;
		$this->htmlEl->minlength = $minLength;
		return $this;
	}

	public function getMaxLength(): ?int
	{
		return $this->maxLength;
	}

	/**
	 * @return $this
	 */
	public function setMaxLength(?int $maxLength): self
	{
		$this->maxLength = $maxLength;
		$this->htmlEl->maxlength = $maxLength;
		return $this;
	}

	/**
	 * @return $this
	 */
	public function setPlaceholder(?string $placeholder): self
	{
		$this->htmlEl->placeholder = $placeholder;
		return $this;
	}

	/**
	 * @return $this
	 */
	public function setAutocomplete(?string $autocomplete): self
	{
		$this->htmlEl->autocomplete = $autocomplete;
		return $this;
	}

	protected function validateMinLength(): bool
	{
		return $this->setErrorIf(
			$this->minLength !== null && strlen($this->value) < $this->minLength,
			"Prosím zadejte alespoň $this->minLength znaků."
		);
	}

	protected function validateMaxLength(): bool
	{
		return $this->setErrorIf(
			$this->maxLength !== null && strlen($this->value) > $this->maxLength,
			"Prosím zadejte maximálně $this->maxLength znaků."
		);
	}

}
