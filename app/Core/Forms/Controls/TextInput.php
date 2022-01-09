<?php

declare(strict_types=1);

namespace Core\Forms\Controls;

use Nette\Utils\Validators;

/**
 * HTML input
 * @phpstan-import-type HtmlLabel from HtmlWithLabelControl
 */
class TextInput extends TextBaseControl
{

	public const
		TYPE_TEXT = 'text',
		TYPE_EMAIL = 'email',
		TYPE_PASSWORD = 'password';

	protected string $type = self::TYPE_TEXT;
	protected ?string $typeMsg = null;

	/** @var array<array{string, ?string}> */
	protected array $patterns = [];

	protected bool $outputPasswordValueEnabled = false;

	/**
	 * @param string $name
	 * @param HtmlLabel $label
	 */
	public function __construct(string $name, $label)
	{
		parent::__construct($name, 'input', $label);
		$this->defaultValidators = [
			'validateMaxLength', // to prevent RegExp DoS and similar attacks, limit the max length first
			'validateType',
			'validatePatterns',
			'validateMinLength',
		];
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

	/**
	 * @return $this
	 */
	public function setOutputPasswordValueEnabled(bool $outputPasswordValueEnabled): self
	{
		$this->outputPasswordValueEnabled = $outputPasswordValueEnabled;
		return $this;
	}

	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @return $this
	 */
	public function setType(string $type, ?string $msg = null): self
	{
		$this->type = $type;
		$this->typeMsg = $msg;
		$this->htmlEl->type = $type;
		$this->htmlEl->attrs['data-type-msg'] = $msg;
		return $this;
	}

	private static function isStartEndAnchoredPattern(string $pattern): bool
	{
		// TODO: use the following implementation once we switch to PHP 8+
		// return str_starts_with($pattern, '^') && str_ends_with($pattern, '$');
		return strlen($pattern) >= 2 && $pattern[0] === '^' && $pattern[-1] === '$';
	}

	/**
	 * @return $this
	 */
	public function addPattern(string $pattern, ?string $msg = null): self
	{
		$this->patterns[] = [$pattern, $msg];

		// reset element attributes
		$this->htmlEl->pattern = null;
		$this->htmlEl->attrs['data-pattern-msg'] = null;
		$this->htmlEl->attrs['data-patterns'] = null;

		if (count($this->patterns) === 1 && self::isStartEndAnchoredPattern($this->patterns[0][0])) {
			// HTML5 pattern attribute supports only one start-end anchored pattern
			// see https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/pattern#constraint_validation
			$this->htmlEl->pattern = $this->patterns[0][0];
			$this->htmlEl->attrs['data-pattern-msg'] = $this->patterns[0][1];
		} else if (count($this->patterns) > 0/* @phpstan-ignore-line */) {
			// use custom patterns validation implementation
			// in case we have more than one pattern and/or non-start-end anchored pattern
			$this->htmlEl->attrs['data-patterns'] = $this->patterns;
		}

		return $this;
	}

	protected function validateType(): bool
	{
		if ($this->type === self::TYPE_EMAIL) {
			// TODO: maybe replace Validators::isEmail() with custom implementation
			if (!Validators::isEmail($this->value ?? '')) {
				$this->setError('E-mailová adresa nemá správný formát.');
				return false;
			}
		}
		return true;
	}

	protected function validatePatterns(): bool
	{
		foreach ($this->patterns as $pattern) {
			// use the full Unicode mode (flag u) to be compatible with HTML5 pattern attribute
			// see https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/pattern
			if (preg_match("/$pattern[0]/u", $this->value ?? '') !== 1) {
				$this->setError($pattern[1] ?? 'Zadejte hodnotu, která odpovídá požadovanému formátu.');
				return false;
			}
		}

		return true;
	}

}
