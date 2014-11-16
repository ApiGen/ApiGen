<?php

namespace Project;


class Annotations
{

	/**
	 * @uses Foo::getName()
	 * @param Foo $foo This is description of foo.
	 * @return string
	 */
	public function testMe(Foo $foo)
	{
		return $foo->getName();
	}


	/**
	 * @param Foo $foo This is description with **bold**.
	 */
	public function testBold(Foo $foo)
	{
	}


	/**
	 * @param Foo $foo This is simple multi line description, there might be some `code`.
	 *  This is second line of simple multi line description.
	 * @return string The data as accepted by the inspector, or partial data if `$maxLength` or `EOF` were encountered.
	 */
	public function testMultilineParam(Foo $foo)
	{
	}

}
