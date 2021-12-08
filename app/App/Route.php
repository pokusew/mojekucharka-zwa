<?php

namespace App;

class Route
{

	public string $pattern;
	public string $regex;
	public string $presenter;

	public function __construct(string $pattern, string $presenter, bool $placeholders = false)
	{
		$this->pattern = $pattern;
		// $this->regex = $regex;
		$this->presenter = $presenter;
	}

	public function match(string $path): ?array
	{
		if ($this->pattern === $path) {
			return [];
		}
		return null;
	}

}
