<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\Elements\ElementsInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionInterface;
use Nette;
use TokenReflection\IReflection;
use TokenReflection\IReflectionClass;
use TokenReflection\IReflectionFunction;
use TokenReflection\IReflectionMethod;
use TokenReflection\IReflectionParameter;
use TokenReflection\IReflectionProperty;

abstract class ReflectionBase extends Nette\Object implements ReflectionInterface
{

    /**
     * @var string
     */
    protected $reflectionType;

    /**
     * @var IReflectionClass|IReflectionFunction|IReflectionMethod|IReflectionParameter|IReflectionProperty
     */
    protected $reflection;

    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var ParserStorageInterface
     */
    protected $parserResult;

    /**
     * @var ReflectionFactoryInterface
     */
    protected $reflectionFactory;


    public function __construct(IReflection $reflection)
    {
        $this->reflectionType = get_class($this);
        $this->reflection = $reflection;
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->reflection->getName();
    }


    /**
     * {@inheritdoc}
     */
    public function getPrettyName()
    {
        return $this->reflection->getPrettyName();
    }


    /**
     * {@inheritdoc}
     */
    public function isInternal()
    {
        return $this->reflection->isInternal();
    }


    /**
     * {@inheritdoc}
     */
    public function isTokenized()
    {
        return $this->reflection->isTokenized();
    }


    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->reflection->getFileName();
    }


    /**
     * @return int
     */
    public function getStartLine()
    {
        $startLine = $this->reflection->getStartLine();
        if ($doc = $this->getDocComment()) {
            $startLine -= substr_count($doc, "\n") + 1;
        }
        return $startLine;
    }


    /**
     * @return int
     */
    public function getEndLine()
    {
        return $this->reflection->getEndLine();
    }


    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }


    public function setParserResult(ParserStorageInterface $parserResult)
    {
        $this->parserResult = $parserResult;
    }


    public function setReflectionFactory(ReflectionFactoryInterface $reflectionFactory)
    {
        $this->reflectionFactory = $reflectionFactory;
    }


    /**
     * @return ClassReflectionInterface[]
     */
    public function getParsedClasses()
    {
        return $this->parserResult->getElementsByType(ElementsInterface::CLASSES);
    }
}
