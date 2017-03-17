<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Resolvers\ElementResolver;

use ApiGen\Contracts\Generator\Resolvers\ElementResolverInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Tests\ContainerFactory;
use PHPUnit\Framework\TestCase;

abstract class AbstractElementResolverTest extends TestCase
{
    /**
     * @var ElementResolverInterface
     */
    protected $elementResolver;

    /**
     * @var ParserStorageInterface
     */
    protected $parserStorage;


    protected function setUp(): void
    {
        $container = (new ContainerFactory())->create();

        $this->elementResolver = $container->getByType(ElementResolverInterface::class);
        $this->parserStorage = $container->getByType(ParserStorageInterface::class);
    }
}
