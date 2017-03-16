<?php declare(strict_types=1);

namespace ApiGen\Templating;

use Latte\Engine;

class Template
{

    /**
     * @var Engine
     */
    private $latteEngine;

    /**
     * @var string
     */
    private $savePath;

    /**
     * @var string
     */
    private $file;

    /**
     * @var mixed[]
     */
    private $parameters = [];


    public function __construct(Engine $latteEngine)
    {
        $this->latteEngine = $latteEngine;
    }


    public function setFile(string $file): void
    {
        $this->file = $file;
    }


    /**
     * @return mixed[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }


    /**
     * @return self
     */
    public function setParameters(array $parameters): self
    {
        $this->parameters = $parameters + $this->parameters;
        return $this;
    }


    public function setSavePath(string $savePath): void
    {
        $this->savePath = $savePath;
    }


    public function save(string $file = null): void
    {
        $this->savePath = $file ?: $this->savePath;
        $dir = dirname($this->savePath);

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $content = $this->latteEngine->renderToString($this->file, $this->parameters);
        file_put_contents($this->savePath, $content);
    }
}
