<?php declare(strict_types=1);

namespace ApiGen\Tests\Parser\Reflection;

use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassPropertyReflectionInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use ApiGen\Tests\Parser\Reflection\ReflectionMethodSource\ReflectionMethod;

final class ReflectionPropertyTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ClassReflectionInterface
     */
    private $reflectionClass;

    /**
     * @var ClassPropertyReflectionInterface
     */
    private $reflectionProperty;

    protected function setUp(): void
    {
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parserStorage = $parser->parseDirectories([__DIR__ . '/ReflectionMethodSource']);

        $this->reflectionClass = $parserStorage->getClasses()[ReflectionMethod::class];
        $this->reflectionProperty = $this->reflectionClass->getProperty('memberCount');
    }


}
