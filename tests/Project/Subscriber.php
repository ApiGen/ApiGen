<?php

namespace Project;


interface Subscriber
{

	/**
	 * @return array
	 */
	function getHooks();


	/**
	 * @return int
	 */
	function getPriority();

}
