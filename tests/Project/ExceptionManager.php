<?php

namespace Project;


class ExceptionManager
{

	/**
	 * @throws \RuntimeException When this happens.
	 */
	public function getForbiddenMethod()
	{
	}


	/**
	 * @throws ForbiddenCallException This happens every time.
	 */
	public function getLocalMethod()
	{
	}


	/**
	 * @throws \RuntimeException One comment.
	 * @throws ForbiddenCallException Another comment.
	 */
	public function throwAllExceptions()
	{
	}

}



class ForbiddenCallException extends \Exception
{
}
