<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use Nette\Utils\Finder;
use PHPUnit\Framework\Assert;
use ReflectionProperty;
use SplFileInfo;

final class ParserTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ParserInterface
     */
    private $parser;

    /**
     * @var ParserStorageInterface
     */
    private $parserStorage;

    protected function setUp(): void
    {
        $this->parser = $this->container->getByType(ParserInterface::class);
        $this->parserStorage = $this->container->getByType(ParserStorageInterface::class);

        /** @var ConfigurationInterface $configuration */
        $configuration = $this->container->getByType(ConfigurationInterface::class);
        $configuration->setOptions(['visibilityLevels' => ReflectionProperty::IS_PUBLIC]);
    }

    public function testGetFunctions(): void
    {
        $this->parser->parse($this->getFilesFromDir(__DIR__ . '/ParserSource'));
        $functions = $this->parserStorage->getFunctions();
        $this->assertCount(1, $functions);

        $function = array_pop($functions);
        $this->assertInstanceOf(FunctionReflectionInterface::class, $function);

        $this->checkLoadedProperties($function);
    }

    public function testGetConstants(): void
    {
        $this->parser->parse($this->getFilesFromDir(__DIR__ . '/ParserSource'));
        $constants = $this->parserStorage->getConstants();
        $this->assertCount(1, $constants);

        $constant = array_pop($constants);
        $this->assertInstanceOf(ConstantReflectionInterface::class, $constant);

        $this->checkLoadedProperties($constant);
    }

    /**
     * @expectedException \TokenReflection\Exception\FileProcessingException
     */
    public function testParseError(): void
    {
        $this->assertCount(0, $this->parserStorage->getClasses());

        $this->parser->parse($this->getFilesFromDir(__DIR__ . '/ErrorParseSource'));
    }

    public function testParseClasses(): void
    {
        $this->assertCount(0, $this->parserStorage->getClasses());

        $this->parser->parse($this->getFilesFromDir(__DIR__ . '/ParserSource'));

        $classes = $this->parserStorage->getClasses();
        $this->assertCount(3, $classes);

        $class = array_pop($classes);
        $this->assertInstanceOf(ClassReflectionInterface::class, $class);
        $this->checkLoadedProperties($class);
    }

    /**
     * @param string $dir
     * @return SplFileInfo[]
     */
    private function getFilesFromDir(string $dir): array
    {
        $files = [];
        foreach (Finder::find('*.php')->in($dir) as $splFile) {
            $files[] = $splFile;
        }

        return $files;
    }

    /**
     * @param object $object
     */
    private function checkLoadedProperties($object): void
    {
        $this->assertInstanceOf(
            ConfigurationInterface::class,
            Assert::getObjectAttribute($object, 'configuration')
        );

        $this->assertInstanceOf(
            ParserStorageInterface::class,
            Assert::getObjectAttribute($object, 'parserStorage')
        );

        $this->assertInstanceOf(
            ReflectionFactoryInterface::class,
            Assert::getObjectAttribute($object, 'reflectionFactory')
        );
    }
}
