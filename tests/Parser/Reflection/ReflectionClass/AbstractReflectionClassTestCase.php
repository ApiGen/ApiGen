<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflection\ReflectionClass;

use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use Project\AccessLevels;
use Project\ParentClass;
use Project\RichInterface;
use Project\SomeTrait;

abstract class AbstractReflectionClassTestCase extends AbstractContainerAwareTestCase
{
    /**
     * @var ReflectionClass
     */
    protected $reflectionClass;

    /**
     * @var ReflectionClass
     */
    protected $reflectionClassOfParent;

    /**
     * @var ReflectionClass
     */
    protected $reflectionClassOfTrait;

    /**
     * @var ReflectionClass
     */
    protected $reflectionClassOfInterface;

    protected function setUp(): void
    {
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parserStorage = $parser->parseDirectories([__DIR__ . '/../ReflectionClassSource']);

        $this->reflectionClass = $parserStorage->getClasses()[AccessLevels::class];
        $this->reflectionClassOfParent = $parserStorage->getClasses()[ParentClass::class];
        $this->reflectionClassOfTrait = $parserStorage->getClasses()[SomeTrait::class];
        $this->reflectionClassOfInterface = $parserStorage->getClasses()[RichInterface::class];
    }
}
