<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Tests\ContainerAwareTestCase;
use Nette\Utils\Finder;
use ReflectionProperty;
use SplFileInfo;

final class ParserTest extends ContainerAwareTestCase
{
    /**
     * @var ParserInterface
     */
    private $parser;

    /**
     * @var ParserStorageInterface
     */
    private $parserResult;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;


    protected function setUp(): void
    {
        $this->parser = $this->container->getByType(ParserInterface::class);
        $this->parserResult = $this->container->getByType(ParserStorageInterface::class);
        $this->configuration = $this->container->getByType(ConfigurationInterface::class);
        /** @var ConfigurationInterface $configuration */
        $configuration = $this->container->getByType(ConfigurationInterface::class);
        $configuration->setOptions(['visibilityLevels' => ReflectionProperty::IS_PUBLIC]);
    }


    /**
     * @expectedException \TokenReflection\Exception\FileProcessingException
     */
    public function testParseError(): void
    {
        $this->assertCount(0, $this->parserResult->getClasses());

        $this->parser->parse($this->getFilesFromDir(__DIR__ . '/ErrorParseSource'));
    }


    public function testParseClasses(): void
    {
        $this->assertCount(0, $this->parserResult->getClasses());

        $this->parser->parse($this->getFilesFromDir(__DIR__ . '/ParserSource'));
        $this->assertCount(3, $this->parserResult->getClasses());
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
