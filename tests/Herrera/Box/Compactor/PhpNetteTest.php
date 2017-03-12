<?php

namespace ApiGen\Tests\Herrera\Box\Compactor;

use ApiGen\Herrera\Box\Compactor\PhpNette;
use PHPUnit\Framework\TestCase;

class PhpNetteTest extends TestCase
{

    /**
     * @var PhpNette
     */
    private $phpNetteCompactor;


    protected function setUp()
    {
        $this->phpNetteCompactor = new PhpNette;
    }


    public function testCompactCommon()
    {
        $input = <<<INPUT
<?php

/**
 * Some comment
 */
function getSome()
INPUT;
        $expected = <<<COMPACT
<?php




function getSome()
COMPACT;
        $this->assertSame($expected, $this->phpNetteCompactor->compact($input));
    }


    public function testCompactMethodAndReturnAnnotations()
    {
        $input = <<<INPUT
<?php

/**
 * @author ApiGen
 * @method getThis()
 * @return That
 */
function getSomeMore()
INPUT;
        $expected = <<<COMPACT
<?php

/**
 * @author ApiGen
 * @method getThis()
 * @return That
 */
function getSomeMore()
COMPACT;
        $this->assertSame($expected, $this->phpNetteCompactor->compact($input));
    }


    public function testSupports()
    {
        $this->assertTrue($this->phpNetteCompactor->supports('file.php'));
        $this->assertFalse($this->phpNetteCompactor->supports('file.json'));
    }
}
