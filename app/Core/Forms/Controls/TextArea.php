<?php

declare(strict_types=1);

namespace Core\Forms\Controls;

class TextArea extends HtmlWithLabelControl
{

	public function __construct(string $name, $label)
	{
		parent::__construct($name, 'textarea', $label);
	}

}
