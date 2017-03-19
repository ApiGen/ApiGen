<?php declare(strict_types=1);

namespace ApiGen\Tests;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
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


    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->container = (new ContainerFactory)->create();
        $this->sourceDir = $this->container->getParameters()['appDir'] . '/Project';
        $this->destinationDir = $this->container->getParameters()['tempDir'] . '/api';

        $configuration = $this->container->getByType(ConfigurationInterface::class);
        $configuration->resolveOptions([
            'source' => __DIR__,
            'destination' => TEMP_DIR,
        ]);
    }


    protected function getFileContentInOneLine(string $file): string
    {
        $content = file_get_contents($file);
        $content = preg_replace('/\s+/', ' ', $content);
        $content = preg_replace('/(?<=>)\s+|\s+(?=<)/', '', $content);

        return $content;
    }
}
