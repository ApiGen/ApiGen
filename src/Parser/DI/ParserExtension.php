<?php

namespace ApiGen\Parser\DI;

use ApiGen\Parser\Broker\Backend;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use TokenReflection\Broker;

class ParserExtension extends CompilerExtension
{

    public function loadConfiguration()
    {
        $this->loadServicesFromConfig();

        $builder = $this->getContainerBuilder();

        $backend = $builder->addDefinition($this->prefix('backend'))
            ->setClass(Backend::class);

        $builder->addDefinition($this->prefix('broker'))
            ->setClass(Broker::class)
            ->setArguments([
                $backend,
                Broker::OPTION_DEFAULT & ~(Broker::OPTION_PARSE_FUNCTION_BODY | Broker::OPTION_SAVE_TOKEN_STREAM)
            ]);
    }


    private function loadServicesFromConfig()
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/services.neon')['services']
        );
    }
}
