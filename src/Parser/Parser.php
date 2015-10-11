<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Parser;

use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ArrayObject;
use TokenReflection\Broker;
use TokenReflection\Broker\Backend;
use TokenReflection\Exception\FileProcessingException;
use TokenReflection\Exception\ParseException;

class Parser implements ParserInterface
{

    /**
     * @var Broker
     */
    private $broker;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var ParserStorageInterface
     */
    private $parserStorage;


    public function __construct(Broker $broker, ParserStorageInterface $parserResult)
    {
        $this->broker = $broker;
        $this->parserStorage = $parserResult;
    }


    /**
     * {@inheritdoc}
     */
    public function parse(array $files)
    {
        foreach ($files as $file) {
            try {
                $this->broker->processFile($file->getPathname());
            } catch (ParseException $exception) {
                $this->errors[] = new FileProcessingException([$exception]);
            } catch (FileProcessingException $exception) {
                $this->errors[] = $exception;
            }
        }

        $this->extractBrokerDataForParserResult($this->broker);
        return $this->parserStorage;
    }


    /**
     * {@inheritdoc}
     */
    public function getErrors()
    {
        return $this->errors;
    }


    private function extractBrokerDataForParserResult(Broker $broker)
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
    ) {
        $this->parserStorage->setClasses($classes);
        $this->parserStorage->setConstants($constants);
        $this->parserStorage->setFunctions($functions);
        $this->parserStorage->setInternalClasses($internalClasses);
        $this->parserStorage->setTokenizedClasses($tokenizedClasses);
    }
}
