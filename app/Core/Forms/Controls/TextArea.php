<?php

declare(strict_types=1);

namespace Core\Forms\Controls;

class TextArea extends TextBase
{

	public function __construct(string $name, string $label)
	{
		parent::__construct($name, $label);
		$this->htmlEl->setName('textarea');
	}

}
