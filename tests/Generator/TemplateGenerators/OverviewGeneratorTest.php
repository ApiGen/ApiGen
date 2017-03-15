<?php declare(strict_types=1);

namespace ApiGen\Tests\ApiGen\Generator\TemplateGenerators;

use ApiGen\Generator\TemplateGenerators\OverviewGenerator;
use ApiGen\Templating\Template;
use ApiGen\Templating\TemplateFactory;
use Mockery;
use PHPUnit\Framework\TestCase;

class OverviewGeneratorTest extends TestCase
{

    public function testGenerate()
    {
        $templateFactoryMock = $this->getTemplateFactoryMock();
        $overviewGenerator = new OverviewGenerator($templateFactoryMock);
        $overviewGenerator->generate();
        $this->assertFileExists(TEMP_DIR . '/index.html');
    }


    /**
     * @return Mockery\MockInterface
     */
    private function getTemplateFactoryMock()
    {
        $templateFactoryMock = Mockery::mock(TemplateFactory::class);
        $templateFactoryMock->shouldReceive('createForType')->andReturn($this->getTemplateMock());
        $templateFactoryMock->shouldReceive('save');
        return $templateFactoryMock;
    }


    /**
     * @return Mockery\MockInterface
     */
    private function getTemplateMock()
    {
        $templateMock = Mockery::mock(Template::class);
        $templateMock->shouldReceive('setSavePath')->withAnyArgs();
        $templateMock->shouldReceive('save')->andReturn(file_put_contents(TEMP_DIR . '/index.html', '...'));
        return $templateMock;
    }
}
