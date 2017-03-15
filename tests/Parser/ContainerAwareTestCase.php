<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests;

use Nette\DI\Container;
use PHPUnit\Framework\TestCase;

abstract class ContainerAwareTestCase extends TestCase
{

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $sourceDir;

    /**
     * @var string
     */
    protected $destinationDir;


    public function __construct()
    {
        $this->container = (new ContainerFactory)->create();
        $this->sourceDir = $this->container->getParameters()['appDir'] . '/Project';
        $this->destinationDir = $this->container->getParameters()['tempDir'] . '/api';
    }


    /**
     * @param string $file
     * @return string
     */
    protected function getFileContentInOneLine($file)
    {
        $content = file_get_contents($file);
        $content = preg_replace('/\s+/', ' ', $content);
        $content = preg_replace('/(?<=>)\s+|\s+(?=<)/', '', $content);
        return $content;
    }
}
