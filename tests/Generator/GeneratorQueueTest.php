<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Contracts\Console\Helper\ProgressBarInterface;
use ApiGen\Contracts\Generator\StepCounterInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\ConditionalTemplateGeneratorInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;
use ApiGen\Generator\GeneratorQueue;
use ApiGen\Tests\MethodInvoker;
use PHPUnit\Framework\TestCase;

final class GeneratorQueueTest extends TestCase
{
    /**
     * @var GeneratorQueue
     */
    private $generatorQueue;


    protected function setUp(): void
    {
        $progressBarMock = $this->createMock(ProgressBarInterface::class);
        $this->generatorQueue = new GeneratorQueue($progressBarMock);
    }


    public function testRun(): void
    {
        $this->assertFileNotExists(TEMP_DIR . '/file.txt');

        $templateGeneratorMock = $this->createMock(TemplateGeneratorInterface::class);
        $templateGeneratorMock->method('generate')
            ->willReturn(file_put_contents(TEMP_DIR . '/file.txt', '...'));
        $this->generatorQueue->addToQueue($templateGeneratorMock);
        $this->generatorQueue->run();

        $this->assertFileExists(TEMP_DIR . '/file.txt');
    }


    public function testGetAllowedQueue(): void
    {
        $this->generatorQueue->addToQueue($this->createConditionalTemplateGenerator());

        $this->assertCount(0, MethodInvoker::callMethodOnObject($this->generatorQueue, 'getAllowedQueue'));
    }


    public function testGetStepCount(): void
    {
        $templateGeneratorMock = $this->createMock([TemplateGeneratorInterface::class, StepCounterInterface::class]);
        $templateGeneratorMock->method('getStepCount')
            ->willReturn(50);
        $this->generatorQueue->addToQueue($templateGeneratorMock);

        $this->assertSame(50, MethodInvoker::callMethodOnObject($this->generatorQueue, 'getStepCount'));
    }

    private function createConditionalTemplateGenerator(): ConditionalTemplateGeneratorInterface
    {
        return new class implements ConditionalTemplateGeneratorInterface
        {
            public function isAllowed(): bool
            {
                return false;
            }

            public function generate(): void
            {
            }
        };
    }
}
