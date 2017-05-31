<?php declare(strict_types=1);

namespace ApiGen\Annotation\DI;

use ApiGen\Annotation\AnnotationDecorator;
use ApiGen\Contracts\Annotation\AnnotationSubscriberInterface;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;
use Symplify\PackageBuilder\Adapter\Nette\DI\DefinitionCollector;

final class AnnotationExtension extends CompilerExtension
{
    public function loadConfiguration(): void
    {
        Compiler::loadDefinitions(
            $this->getContainerBuilder(),
            $this->loadFromFile(__DIR__ . '/../config/services.neon')
        );
    }

    public function beforeCompile(): void
    {
        DefinitionCollector::loadCollectorWithType(
            $this->getContainerBuilder(),
            AnnotationDecorator::class,
            AnnotationSubscriberInterface::class,
            'addAnnotationSubscriber'
        );
    }
}
