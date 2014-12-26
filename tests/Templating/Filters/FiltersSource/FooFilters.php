<?php

namespace ApiGen\Tests\Templating\Filters\FiltersSource;

use ApiGen\Templating\Filters\Filters;


class FooFilters extends Filters
{

	/**
	 * @param string $text
	 * @return string
	 */
	protected function bazFilter($text)
	{
		return 'Filtered: ' . $text;
	}

}
