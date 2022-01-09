<?php

declare(strict_types=1);

namespace Core\Forms\Controls;

/**
 * HTML select
 * @phpstan-type Options array<string|int, mixed>
 * @phpstan-import-type HtmlLabel from HtmlWithLabelControl
 */
class Select extends HtmlWithLabelControl
{

	/**
	 * @var array<string, Options>|null
	 */
	protected ?array $groupedOptions;

	/**
	 * @var Options
	 */
	protected array $options;

	/**
	 * @var mixed|null not implemented yet
	 */
	protected $prompt;

	/**
	 * @param string $name
	 * @param HtmlLabel $label
	 */
	public function __construct(string $name, $label)
	{
		parent::__construct($name, 'select', $label);
		$this->options = [];
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
	 * @return $this
	 */
	public function setValue(?string $value): self
	{
		if ($value !== null && $value !== '') {
			// we need isset here because it uses == (weak comparison)
			if (!isset($this->options[$value])) {
				$this->value = null;
				$this->setError('Prosím vyberte platnou hodnotu ze seznamu položek.');
				return $this;
			}
			$this->value = $value;
		}
		return $this;
	}

	/**
	 * @return Options
	 */
	public function getOptions(): array
	{
		return $this->options;
	}

	/**
	 * @param Options $options
	 * @return $this
	 */
	public function setOptions(array $options): self
	{
		$this->options = $options;
		return $this;
	}

	/**
	 * @return array<string, Options>|null
	 */
	public function getGroupedOptions(): ?array
	{
		return $this->groupedOptions;
	}

	/**
	 * @param array<string, Options>|null $groupedOptions
	 * @return $this
	 */
	public function setGroupedOptions(?array $groupedOptions): self
	{
		$this->groupedOptions = $groupedOptions;
		return $this;
	}

}
