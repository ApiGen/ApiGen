<?php declare(strict_types=1);

namespace ApiGen\Parser\DI;

use ApiGen\Parser\Broker\Backend;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use TokenReflection\Broker;

final class ParserExtension extends CompilerExtension
{
    public function loadConfiguration(): void
    {
        $this->loadServicesFromConfig();

        $containerBuilder = $this->getContainerBuilder();

        $backend = $containerBuilder->addDefinition($this->prefix('backend'))
            ->setClass(Backend::class);

        $containerBuilder->addDefinition($this->prefix('broker'))
            ->setClass(Broker::class)
            ->setArguments([
                $backend,
                Broker::OPTION_DEFAULT & ~(Broker::OPTION_PARSE_FUNCTION_BODY | Broker::OPTION_SAVE_TOKEN_STREAM)
            ]);
    }

    private function loadServicesFromConfig(): void
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/services.neon')['services']
        );
    }
}
