<?php

declare(strict_types=1);

namespace Core\Forms\Controls;

use Core\Forms\Form;
use Nette\Utils\Html;

abstract class TextBase extends BaseControl
{

	protected ?int $minLength = null;
	protected ?int $maxLength = null;

	protected Html $htmlEl;
	protected Html $htmlLabelEl;

	public function __construct(string $name, string $label)
	{
		parent::__construct($name);
		$this->htmlEl = Html::el('input');
		$this->htmlLabelEl = Html::el('label');
		$this->htmlLabelEl->setText($label);
		$this->generateId();
		$this->defaultValidators[] = 'validateMinLength';
		$this->defaultValidators[] = 'validateMaxLength';
	}

	public function getElem(): Html
	{
		return $this->htmlEl;
	}

	public function getLabel(): Html
	{
		return $this->htmlLabelEl;
	}

	protected function generateId()
	{
		$id = ($this->form !== null ? $this->form->getName() . '--' : '') . $this->name;
		$this->htmlEl->id = $id;
		$this->htmlLabelEl->for = $id;
	}

	public function setForm(?Form $form): self
	{
		parent::setForm($form);
		$this->generateId();
		return $this;
	}

	public function setRequired(bool $required = true): self
	{
		parent::setRequired($required);
		$this->htmlEl->required = $required;
		return $this;
	}

	public function getMinLength(): ?int
	{
		return $this->minLength;
	}

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

	public function setMaxLength(?int $maxLength): self
	{
		$this->maxLength = $maxLength;
		$this->htmlEl->maxlength = $maxLength;
		return $this;
	}

	public function setPlaceholder(?string $placeholder): self
	{
		$this->htmlEl->placeholder = $placeholder;
		return $this;
	}

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

	public function __toString(): string
	{
		return (string) $this->htmlEl;
	}

}
