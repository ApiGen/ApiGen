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

		$builder->addDefinition($this->prefix('parser'))
			->setClass('ApiGen\Parser\Parser');

		$builder->addDefinition($this->prefix('parserResult'))
			->setClass('ApiGen\Parser\ParserResult');

		$backend = $builder->addDefinition($this->prefix('backend'))
			->setClass('ApiGen\Parser\Broker\Backend');

		$builder->addDefinition($this->prefix('broker'))
			->setClass('TokenReflection\Broker')
			->setArguments([
				$backend,
				Broker::OPTION_DEFAULT & ~(Broker::OPTION_PARSE_FUNCTION_BODY | Broker::OPTION_SAVE_TOKEN_STREAM)
			]);

		$builder->addDefinition($this->prefix('elements'))
			->setClass('ApiGen\Parser\Elements\Elements');

		$builder->addDefinition($this->prefix('autocopmlete'))
			->setClass('ApiGen\Parser\Elements\AutocompleteElements');

		$builder->addDefinition($this->prefix('elementExtractor'))
			->setClass('ApiGen\Parser\Elements\ElementExtractor');

		$builder->addDefinition($this->prefix('elementFilter'))
			->setClass('ApiGen\Parser\Elements\ElementFilter');

		$builder->addDefinition($this->prefix('elementSorter'))
			->setClass('ApiGen\Parser\Elements\ElementSorter');

		$builder->addDefinition($this->prefix('elementStorage'))
			->setClass('ApiGen\Parser\Elements\ElementStorage');

		$builder->addDefinition($this->prefix('groupSorter'))
			->setClass('ApiGen\Parser\Elements\GroupSorter');

		$builder->addDefinition($this->prefix('reflectionFactory'))
			->setClass('ApiGen\Reflection\TokenReflection\ReflectionFactory');

		$builder->addDefinition($this->prefix('reflectionCrateBridge'))
			->setClass('ApiGen\Reflection\TokenReflection\ReflectionCrateBridge');
	}

}
