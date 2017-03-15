<?php declare(strict_types=1);

namespace ApiGen\Utils\Tests\DI;

use ApiGen\Utils\DI\UtilsExtension;
use Nette\DI\Compiler;
use Nette\DI\ContainerBuilder;
use Nette\DI\ServiceDefinition;
use PHPUnit\Framework\TestCase;

class UtilsExtensionTest extends TestCase
{

    public function testLoadConfiguration(): void
    {
        $utilsExtension = new UtilsExtension;
        $utilsExtension->setCompiler(new Compiler(new ContainerBuilder), 'compiler');
        $utilsExtension->loadConfiguration();

        $builder = $utilsExtension->getContainerBuilder();
        $builder->prepareClassList();

        $found = $builder->findByType('ApiGen\Utils\FileSystem');
        /** @var ServiceDefinition $fileSystemDefinition */
        $fileSystemDefinition = array_pop($found);
        $this->assertSame('ApiGen\Utils\FileSystem', $fileSystemDefinition->getClass());
    }
}
