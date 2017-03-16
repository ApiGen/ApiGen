<?php declare(strict_types=1);

namespace ApiGen\Tests\ApiGen\Generator\TemplateGenerators;

use ApiGen\Generator\TemplateGenerators\OverviewGenerator;
use ApiGen\Templating\Template;
use ApiGen\Templating\TemplateFactory;
use Mockery;
use PHPUnit\Framework\TestCase;

class OverviewGeneratorTest extends TestCase
{

    public function testGenerate(): void
    {
        $templateFactoryMock = $this->getTemplateFactoryMock();
        $overviewGenerator = new OverviewGenerator($templateFactoryMock);
        $overviewGenerator->generate();
        $this->assertFileExists(TEMP_DIR . '/index.html');
    }


    private function getTemplateFactoryMock(): Mockery\MockInterface
    {
        $templateFactoryMock = Mockery::mock(TemplateFactory::class);
        $templateFactoryMock->shouldReceive('createForType')->andReturn($this->getTemplateMock());
        $templateFactoryMock->shouldReceive('save');
        return $templateFactoryMock;
    }


    private function getTemplateMock(): Mockery\MockInterface
    {
        $templateMock = Mockery::mock(Template::class);
        $templateMock->shouldReceive('setSavePath')->withAnyArgs();
        $templateMock->shouldReceive('save')->andReturn(file_put_contents(TEMP_DIR . '/index.html', '...'));
        return $templateMock;
    }
}
