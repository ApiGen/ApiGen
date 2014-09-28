<?php

namespace ApiGenTests\ApiGen\Project;


class DeprecatedMethod
{

	/**
	 * @return string
	 * @deprecated
	 */
	public function getDrink()
	{
		return 'water';
	}

}
