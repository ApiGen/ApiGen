<?php declare(strict_types=1);

namespace ApiGen\Tests\Parser\Reflection\ReflectionClass;

use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Reflection\Contract\Reflection\ClassReflectionInterface;
use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use ApiGen\Tests\Parser\Reflection\ReflectionClassSource\AccessLevels;
use ApiGen\Tests\Parser\Reflection\ReflectionClassSource\ParentClass;
use ApiGen\Tests\Parser\Reflection\ReflectionClassSource\RichInterface;
use ApiGen\Tests\Parser\Reflection\ReflectionClassSource\SomeTrait;

abstract class AbstractReflectionClassTestCase extends AbstractContainerAwareTestCase
{
    /**
     * @var ReflectionClass|ClassReflectionInterface
     */
    protected $reflectionClass;

    /**
     * @var ReflectionClass|ClassReflectionInterface
     */
    protected $reflectionClassOfParent;

    /**
     * @var ReflectionClass|ClassReflectionInterface
     */
    protected $reflectionClassOfTrait;

    /**
     * @var ReflectionClass|ClassReflectionInterface
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
