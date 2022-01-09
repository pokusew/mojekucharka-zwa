<?php

declare(strict_types=1);

namespace Core\Forms\Controls;

/**
 * Base class for all text based form controls.
 *
 * It adds minlength and maxlength validation support.
 *
 * TODO: option for normalization of newlines in the input text
 * TODO: option for trimming whitespaces in the input text
 *
 * @phpstan-import-type HtmlLabel from HtmlWithLabelControl
 */
abstract class TextBaseControl extends HtmlWithLabelControl
{

	protected ?int $minLength = null;
	protected ?string $minLengthMsg = null;
	protected ?int $maxLength = null;
	protected ?string $maxLengthMsg = null;

	/**
	 * @param string $name
	 * @param string $htmlElementName
	 * @param HtmlLabel $label
	 */
	public function __construct(string $name, string $htmlElementName, $label)
	{
		parent::__construct($name, $htmlElementName, $label);
		$this->defaultValidators = [
			'validateMaxLength',
			'validateMinLength'
		];
	}

	public function getMinLength(): ?int
	{
		return $this->minLength;
	}

	/**
	 * @return $this
	 */
	public function setMinLength(?int $minLength, ?string $msg = null): self
	{
		$this->minLength = $minLength;
		$this->minLengthMsg = $msg;
		$this->htmlEl->minlength = $minLength;
		$this->htmlEl->attrs['data-minlength-msg'] = $msg;
		return $this;
	}

	public function getMaxLength(): ?int
	{
		return $this->maxLength;
	}

	/**
	 * @return $this
	 */
	public function setMaxLength(?int $maxLength, ?string $msg = null): self
	{
		$this->maxLength = $maxLength;
		$this->maxLengthMsg = $msg;
		$this->htmlEl->maxlength = $maxLength;
		$this->htmlEl->attrs['data-maxlength-msg'] = $msg;
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
	 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/autocomplete
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
			$this->minLength !== null && mb_strlen($this->value, 'UTF-8') < $this->minLength,
			$this->minLengthMsg ?? "Prosím zadejte alespoň $this->minLength znaků.",
		);
	}

	protected function validateMaxLength(): bool
	{
		return $this->setErrorIf(
			$this->maxLength !== null && mb_strlen($this->value, 'UTF-8') > $this->maxLength,
			$this->maxLengthMsg ?? "Prosím zadejte maximálně $this->maxLength znaků.",
		);
	}

}
