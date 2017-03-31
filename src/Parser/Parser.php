<?php declare(strict_types=1);

namespace ApiGen\Parser;

use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use Exception;
use SplFileInfo;
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

    /**
     * @param SplFileInfo[] $files
     */
    public function parse(array $files): ParserStorageInterface
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

        $constants = $broker->getConstants();
        $functions = $broker->getFunctions();

        uksort($classes, 'strcasecmp');
        uksort($constants, 'strcasecmp');
        uksort($functions, 'strcasecmp');

        $this->loadToParserStorage($classes, $constants, $functions);
    }

    /**
     * @param ClassReflectionInterface[] $classes
     * @param ConstantReflectionInterface[] $constants
     * @param FunctionReflectionInterface[] $functions
     */
    private function loadToParserStorage(array $classes, array $constants, array $functions): void
    {
        $this->parserStorage->setClasses($classes);
        $this->parserStorage->setConstants($constants);
        $this->parserStorage->setFunctions($functions);
    }
}
