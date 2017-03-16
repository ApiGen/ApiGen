<?php declare(strict_types=1);

namespace ApiGen\Parser;

use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ArrayObject;
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


    public function __construct(Broker $broker, ParserStorageInterface $parserResult)
    {
        $this->broker = $broker;
        $this->parserStorage = $parserResult;
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
        $allFoundClasses = $broker->getClasses(
            Backend::TOKENIZED_CLASSES | Backend::INTERNAL_CLASSES | Backend::NONEXISTENT_CLASSES
        );

        $classes = new ArrayObject($allFoundClasses);
        $constants = new ArrayObject($broker->getConstants());
        $functions = new ArrayObject($broker->getFunctions());
        $internalClasses = new ArrayObject($broker->getClasses(Backend::INTERNAL_CLASSES));
        $tokenizedClasses = new ArrayObject($broker->getClasses(Backend::TOKENIZED_CLASSES));

        $classes->uksort('strcasecmp');
        $constants->uksort('strcasecmp');
        $functions->uksort('strcasecmp');

        $this->loadToParserResult($classes, $constants, $functions, $internalClasses, $tokenizedClasses);
    }


    private function loadToParserResult(
        ArrayObject $classes,
        ArrayObject $constants,
        ArrayObject $functions,
        ArrayObject $internalClasses,
        ArrayObject $tokenizedClasses
    ): void {
        $this->parserStorage->setClasses($classes);
        $this->parserStorage->setConstants($constants);
        $this->parserStorage->setFunctions($functions);
        $this->parserStorage->setInternalClasses($internalClasses);
        $this->parserStorage->setTokenizedClasses($tokenizedClasses);
    }
}
