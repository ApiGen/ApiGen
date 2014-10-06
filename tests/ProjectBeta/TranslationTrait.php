<?php // lint >= 5.4

namespace ProjectBeta;


/**
 * @method \Project\Foo     getMagicFoo()
 * @method \Project\Method  getMagicMethod()
 */
trait TranslationTrait
{

	/**
	 * @return string
	 */
	public function getLang()
	{
		return 'cs';
	}


	/**
	 * @return string
	 */
	public function getCode()
	{
		return 'cs_CZ';
	}

}
