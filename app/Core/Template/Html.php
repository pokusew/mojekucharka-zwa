<?php

declare(strict_types=1);

namespace Core\Template;

class Html
{

	/**
	 * Dynamically builds HTML class attribute
	 * Inspired by https://github.com/JedWatson/classnames
	 * @param ...$classes
	 * @return string stringified and escaped classes as the whole attribute class="red blue"
	 */
	public static function attrClass(...$classes): string
	{
		/** @var string[] $list */
		$list = [];
		foreach ($classes as $value) {
			if (is_string($value)) {
				$list[] = htmlspecialchars($value);
			}
			if (is_array($value)) {
				foreach ($value as $name => $condition) {
					if ($condition) {
						$list[] = htmlspecialchars($name);
					}
				}
			}
		}
		$stringified = implode(' ', $list);
		return 'class="' . $stringified . '"';
	}

}
