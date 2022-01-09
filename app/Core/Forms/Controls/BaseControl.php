<?php

declare(strict_types=1);

namespace Core\Forms\Controls;

use Core\Forms\Form;

/**
 * Base class for all form controls.
 */
abstract class BaseControl
{

	protected string $name;

	protected ?string $value = null;

	protected bool $defaultValidationEnabled = true;

	protected bool $required = false;
	protected ?string $requiredMsg = null;

	protected ?string $error = null;

	protected ?Form $form = null;

	/** @var string[] */
	protected array $defaultValidators = [];

	public function __construct(string $name)
	{
		$this->name = $name;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getForm(): ?Form
	{
		return $this->form;
	}

	/**
	 * Sets the binding to the form. This may cause the control's id to regenerated.
	 * @return $this
	 */
	public function setForm(?Form $form): self
	{
		$this->form = $form;
		return $this;
	}

	public function getValue(): ?string
	{
		return $this->value;
	}

	protected function hasValue(): bool
	{
		return $this->value !== null;
	}

	/**
	 * @param mixed[] $data
	 * @return $this
	 */
	public abstract function setValueFromRequest(array &$data): self;

	/**
	 * @return $this
	 */
	public function setValue(?string $value): self
	{
		if ($value === '') {
			$this->value = null;
		} else {
			$this->value = $value;
		}
		return $this;
	}

	/**
	 * @return $this
	 */
	public function setDefaultValue(?string $value): self
	{
		if ($this->form === null || !$this->form->isSubmitted()) {
			$this->setValue($value);
		}
		return $this;
	}

	public function getError(): ?string
	{
		return $this->error;
	}

	/**
	 * @return $this
	 */
	public function setError(?string $error): self
	{
		$this->error = $error;
		return $this;
	}

	public function setErrorIf(bool $cond, ?string $error): bool
	{
		if ($cond) {
			$this->error = $error;
		}
		return !$cond;
	}

	/**
	 * @return $this
	 */
	public function clearError(): self
	{
		$this->error = null;
		return $this;
	}

	public function hasError(): bool
	{
		return $this->error !== null;
	}

	public function isDefaultValidationEnabled(): bool
	{
		return $this->defaultValidationEnabled;
	}

	public function setDefaultValidationEnabled(bool $defaultValidationEnabled): void
	{
		$this->defaultValidationEnabled = $defaultValidationEnabled;
	}

	public function isRequired(): bool
	{
		return $this->required;
	}

	/**
	 * @return $this
	 */
	public function setRequired(bool $required, ?string $msg = null): self
	{
		$this->required = $required;
		$this->requiredMsg = $msg;
		return $this;
	}

	public function isValid(): bool
	{
		return $this->error === null;
	}

	protected function validateRequired(): bool
	{
		return $this->setErrorIf(
			$this->required && !$this->hasValue(),
			'Vyplňte prosím toto pole.'
		);
	}

	protected function defaultValidate(): bool
	{
		$this->clearError();

		if ($this->hasError()) {
			// an error is already set, this control is invalid
			return false;
		} else if ($this->hasValue()) {
			// only run validators if there is a non-null value
			foreach ($this->defaultValidators as $validator) {
				if (!$this->$validator()) {
					// stop on first error
					return false;
				}
			}
			// all validations passed
			return true;
		} elseif ($this->required) {
			$this->setError('Vyplňte prosím toto pole.');
			return false;
		} else {
			// no value but not required
			return true;
		}
	}

	public function validate(): bool
	{
		$this->clearError();

		// TODO: UTF-8 validation

		if ($this->defaultValidationEnabled && !$this->defaultValidate()) {
			return false;
		}

		// TODO: add support for custom validator function

		return true;
	}

}
