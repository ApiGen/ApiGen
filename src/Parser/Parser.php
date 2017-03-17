<?php declare(strict_types=1);

namespace ApiGen\Parser;

use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use TokenReflection\Broker;
use TokenReflection\Broker\Backend;

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


    public function __construct(Broker $broker, ParserStorageInterface $parserStorage)
    {
        $this->broker = $broker;
        $this->parserStorage = $parserStorage;
    }


    public function parse(array $files): ParserStorageInterface
    {
        foreach ($files as $file) {
            $this->broker->processFile($file->getPathname());
        }

        $this->extractBrokerDataForParserResult($this->broker);

        return $this->parserStorage;
    }


    private function extractBrokerDataForParserResult(Broker $broker): void
    {
        $classes = $broker->getClasses(
            Backend::TOKENIZED_CLASSES | Backend::INTERNAL_CLASSES | Backend::NONEXISTENT_CLASSES
        );

        $constants = $broker->getConstants();
        $functions = $broker->getFunctions();
        $tokenizedClasses = $broker->getClasses(Backend::TOKENIZED_CLASSES);

        uksort($allFoundClasses, 'strcasecmp');
        uksort($constants, 'strcasecmp');
        uksort($functions, 'strcasecmp');

        $this->loadToParserStorage($classes, $constants, $functions, $tokenizedClasses);
    }


    private function loadToParserStorage(
        array $classes, array $constants, array $functions, array $tokenizedClasses
    ): void {
        $this->parserStorage->setClasses($classes);
        $this->parserStorage->setConstants($constants);
        $this->parserStorage->setFunctions($functions);
        $this->parserStorage->setTokenizedClasses($tokenizedClasses);
    }
}
