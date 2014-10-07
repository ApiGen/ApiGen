<?php

namespace Project;


class Annotations
{

	/**
	 * @uses Foo::getName()
	 * @param Foo $foo
	 * @return string
	 */
	public function testMe(Foo $foo)
	{
		return $foo->getName();
	}

}
