<?php

namespace Project;


class Implementor implements \Countable, Subscriber
{

	/**
	 * {@inheritdoc}
	 */
	public function count()
	{
		return 1;
	}


	/**
	 * {@inheritdoc}
	 */
	public function getHooks()
	{
		return array('onCreate');
	}


	/**
	 * @inheritdoc
	 */
	public function getPriority()
	{
		return 10;
	}

}
