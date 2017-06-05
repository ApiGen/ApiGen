<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\NamespacesGenerator;
use ApiGen\Reflection\Contract\ParserInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class NamespacesGeneratorTest extends AbstractContainerAwareTestCase
{
    public function test(): void
    {
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parser->parseDirectories([__DIR__ . '/Source']);

        /** @var NamespacesGenerator $namespacesGenerator */
        $namespacesGenerator = $this->container->getByType(NamespacesGenerator::class);
        $namespacesGenerator->generate();

        $this->assertFileExists(TEMP_DIR . '/namespaces.html');
    }
}
