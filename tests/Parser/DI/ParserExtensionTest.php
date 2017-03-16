<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\DI;

use ApiGen\Parser\DI\ParserExtension;
use ApiGen\Parser\Elements\ElementSorter;
use ApiGen\Parser\Tests\Elements\ElementSorterTest;
use ApiGen\Parser\Tests\MethodInvoker;
use Nette\DI\Compiler;
use Nette\DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use TokenReflection\Broker;

class ParserExtensionTest extends TestCase
{

    public function testLoadServicesFromConfig(): void
    {
        $extension = $this->getExtension();
        MethodInvoker::callMethodOnObject($extension, 'loadServicesFromConfig');

        $builder = $extension->getContainerBuilder();
        $builder->prepareClassList();

        $elementResolverDefinition = $builder->getDefinition($builder->getByType(ElementSorter::class));
        $this->assertSame(ElementSorter::class, $elementResolverDefinition->getClass());
    }


    public function testLoadConfiguration(): void
    {
        $extension = $this->getExtension();
        $extension->loadConfiguration();

        $builder = $extension->getContainerBuilder();
        $builder->prepareClassList();

        $brokerDefinition = $builder->getDefinition($builder->getByType(Broker::class));
        $this->assertSame(Broker::class, $brokerDefinition->getClass());
    }


    private function getExtension(): ParserExtension
    {
        $extension = new ParserExtension;
        $extension->setCompiler(new Compiler(new ContainerBuilder), 'compiler');
        return $extension;
    }
}
