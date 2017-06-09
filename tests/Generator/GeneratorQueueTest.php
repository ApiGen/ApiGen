<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Contract\Generator\GeneratorInterface;
use ApiGen\Generator\GeneratorQueue;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

final class GeneratorQueueTest extends AbstractContainerAwareTestCase
{
    /**
     * @var GeneratorQueue
     */
    private $generatorQueue;

    protected function setUp(): void
    {
        $this->disableOutputForProgressBar();

        $this->generatorQueue = $this->container->get(GeneratorQueue::class);
    }

    public function testRun(): void
    {
        $this->assertFileNotExists(TEMP_DIR . '/file.txt');

        $templateGeneratorMock = $this->createMock(GeneratorInterface::class);
        $templateGeneratorMock->method('generate')
            ->willReturn(file_put_contents(TEMP_DIR . '/file.txt', '...'));
        $this->generatorQueue->addGenerator($templateGeneratorMock);
        $this->generatorQueue->run();

        $this->assertFileExists(TEMP_DIR . '/file.txt');
    }

    protected function disableOutputForProgressBar(): void
    {
        /** @var OutputInterface $output */
        $output = $this->container->get(ConsoleOutput::class);
        $output->setVerbosity(0);
    }
}
