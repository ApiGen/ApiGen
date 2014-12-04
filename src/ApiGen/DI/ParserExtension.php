<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\DI;

use Nette\DI\CompilerExtension;
use TokenReflection\Broker;


class ParserExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition('parser')
			->setClass('ApiGen\Parser\Parser');

		$builder->addDefinition('parserResult')
			->setClass('ApiGen\Parser\ParserResult');

		$backend = $builder->addDefinition('backend')
			->setClass('ApiGen\Parser\Broker\Backend');

		$builder->addDefinition($this->prefix('broker'))
			->setClass('TokenReflection\Broker')
			->setArguments([
				$backend,
				Broker::OPTION_DEFAULT & ~(Broker::OPTION_PARSE_FUNCTION_BODY | Broker::OPTION_SAVE_TOKEN_STREAM)
			]);

		$builder->addDefinition('elements')
			->setClass('ApiGen\Parser\Elements\Elements');

		$builder->addDefinition('elementExtractor')
			->setClass('ApiGen\Parser\Elements\ElementExtractor');

		$builder->addDefinition('elementFilter')
			->setClass('ApiGen\Parser\Elements\ElementFilter');

		$builder->addDefinition('elementSorter')
			->setClass('ApiGen\Parser\Elements\ElementSorter');

		$builder->addDefinition('elementStorage')
			->setClass('ApiGen\Parser\Elements\ElementStorage');

		$builder->addDefinition('groupSorter')
			->setClass('ApiGen\Parser\Elements\GroupSorter');
	}

}
