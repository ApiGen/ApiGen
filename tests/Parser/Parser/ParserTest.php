<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use Nette\Utils\Finder;
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
        $this->assertCount(3, $this->parserStorage->getClasses());
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
}
