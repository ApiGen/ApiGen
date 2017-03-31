<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflection\ReflectionClass;

use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use Project\SomeTrait;
use TokenReflection\Broker;
use Project\AccessLevels;
use Project\ParentClass;
use Project\RichInterface;

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
        /** @var Backend $backend */
        $backend = $this->container->getByType(Backend::class);

        /** @var Broker $broker */
        $broker = $this->container->getByType(Broker::class);

        $broker->processDirectory(__DIR__ . '/../ReflectionClassSource');

        $this->reflectionClass = $backend->getClasses()[AccessLevels::class];
        $this->reflectionClassOfParent = $backend->getClasses()[ParentClass::class];
        $this->reflectionClassOfTrait = $backend->getClasses()[SomeTrait::class];
        $this->reflectionClassOfInterface = $backend->getClasses()[RichInterface::class];

        $this->loadParserStorage();
    }

    protected function loadParserStorage(): void
    {
        /** @var ParserStorageInterface $parserStorage */
        $parserStorage = $this->container->getByType(ParserStorageInterface::class);
        $parserStorage->setClasses([
            AccessLevels::class => $this->reflectionClass,
            ParentClass::class => $this->reflectionClassOfParent,
            SomeTrait::class => $this->reflectionClassOfTrait,
            RichInterface::class => $this->reflectionClassOfInterface
        ]);
    }
}
