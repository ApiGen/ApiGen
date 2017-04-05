<?php declare(strict_types=1);

namespace ApiGen\Parser;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Utils\Finder\FinderInterface;
use Exception;
use SplFileInfo;
use TokenReflection\Broker;
use TokenReflection\Broker\Backend;
use TokenReflection\Exception\ParseException;

/**
 * @deprecated Remove with old Parser.
 */
final class Parser implements ParserInterface
{
    /**
     * @var Broker
     */
    private $broker;

    /**
     * @var ParserStorageInterface
     */
    private $parserStorage;

    /**
     * @var FinderInterface
     */
    private $finder;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(
        Broker $broker,
        ParserStorageInterface $parserStorage,
        FinderInterface $finder,
        ConfigurationInterface $configuration
    ) {
        $this->broker = $broker;
        $this->parserStorage = $parserStorage;
        $this->finder = $finder;
        $this->configuration = $configuration;
    }

    /**
     * @param string[] $directories
     */
    public function parseDirectories(array $directories): ParserStorageInterface
    {
        $files = $this->finder->find(
            $directories,
            $this->configuration->getExtensions(),
            $this->configuration->getExclude()
        );

        return $this->parseFiles($files);
    }

    /**
     * @param SplFileInfo[] $files
     */
    public function parseFiles(array $files): ParserStorageInterface
    {
        foreach ($files as $file) {
            try {
                $this->broker->processFile($file->getPathname());
            } catch (ParseException $parseException) {
                throw new Exception(sprintf(
                    'Parser error on %d line with "%s" token. %s',
                    $parseException->getExceptionLine(),
                    $parseException->getTokenName(),
                    $parseException->getDetail()
                ));
            }
        }

        $this->extractBrokerDataForParserStorage($this->broker);

        return $this->parserStorage;
    }

    private function extractBrokerDataForParserStorage(Broker $broker): void
    {
        $classes = $broker->getClasses(Backend::TOKENIZED_CLASSES | Backend::INTERNAL_CLASSES);

        $functions = $broker->getFunctions();

        uksort($classes, 'strcasecmp');
        uksort($functions, 'strcasecmp');

        $this->loadToParserStorage($classes, $functions);
    }

    /**
     * @param ClassReflectionInterface[] $classes
     * @param FunctionReflectionInterface[] $functions
     */
    private function loadToParserStorage(array $classes, array $functions): void
    {
        $this->parserStorage->setClasses($classes);
        $this->parserStorage->setFunctions($functions);
    }
}
