<?php declare(strict_types=1);

namespace ApiGen\Parser;

use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use Exception;
use TokenReflection\Broker;
use TokenReflection\Broker\Backend;
use TokenReflection\Exception\ParseException;

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
            try {
                $this->broker->processFile($file->getPathname());
            } catch (ParseException $parseException) {
                // @todo: make nice exception convertion
                throw new Exception(sprintf(
                    'Parser error on %d line with "%s" token. %s',
                    $parseException->getExceptionLine(),
                    $parseException->getTokenName(),
                    $parseException->getDetail()
                ));
            }
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

        uksort($classes, 'strcasecmp');
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
