<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\ParserStorage;

use ApiGen\Configuration\ConfigurationOptions;
use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Parser\Tests\ParserStorageImplementersSource\ChildInterface;
use ApiGen\Parser\Tests\ParserStorageImplementersSource\ParentInterface;
use ApiGen\Parser\Tests\ParserStorageImplementersSource\SomeClass;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use Nette\Utils\Finder;
use ReflectionProperty;

final class ImplementersTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ParserStorageInterface
     */
    private $parserStorage;

    /**
     * @var ClassReflectionInterface
     */
    private $parentInterfaceReflection;

    protected function setUp(): void
    {
        $finder = Finder::find('*')->in(__DIR__ . '/ImplementersSource');
        $files = iterator_to_array($finder->getIterator());

        /** @var ConfigurationInterface $configuration */
        $configuration = $this->container->getByType(ConfigurationInterface::class);
        $configuration->setOptions([
            ConfigurationOptions::VISIBILITY_LEVELS => ReflectionProperty::IS_PUBLIC
        ]);

        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parser->parseFiles($files);

        $this->parserStorage = $this->container->getByType(ParserStorageInterface::class);
        $classes = $this->parserStorage->getClasses();

        $this->parentInterfaceReflection = $classes[ParentInterface::class];
    }

    public function testGetDirectImplementersOfInterface(): void
    {
        $implementers = $this->parserStorage->getDirectImplementersOfInterface($this->parentInterfaceReflection);
        $this->assertCount(1, $implementers);

        $implementer = $implementers[0];
        $this->assertInstanceOf(ClassReflectionInterface::class, $implementer);
        $this->assertSame(ChildInterface::class, $implementer->getName());
    }

    public function testGetIndirectImplementersOfInterface(): void
    {
        $implementers = $this->parserStorage->getIndirectImplementersOfInterface($this->parentInterfaceReflection);
        $this->assertCount(1, $implementers);

        $implementer = $implementers[0];
        $this->assertInstanceOf(ClassReflectionInterface::class, $implementer);
        $this->assertSame(SomeClass::class, $implementer->getName());
    }
}
