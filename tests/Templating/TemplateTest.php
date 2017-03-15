<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating;

use ApiGen\Templating\Template;
use Latte\Engine;
use PHPUnit\Framework\TestCase;

class TemplateTest extends TestCase
{

    /**
     * @var Template
     */
    private $template;


    protected function setUp()
    {
        $this->template = new Template(new Engine);
    }


    public function testFileIsSavedWithContent()
    {
        $this->template->setFile(__DIR__ . '/TemplateSource/template.latte');
        $this->template->setParameters(['name' => 'World!']);
        $this->template->save(TEMP_DIR . '/dir/hello-world.html');
        $this->assertFileExists(TEMP_DIR . '/dir/hello-world.html');
        $generatedContent = file_get_contents(TEMP_DIR . '/dir/hello-world.html');
        $this->assertSame('Hello World!', trim($generatedContent));
    }
}
