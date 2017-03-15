<?php declare(strict_types=1);

namespace ApiGen\Configuration\Theme;

use ApiGen\Configuration\Exceptions\ConfigurationException;
use ApiGen\Configuration\Readers\NeonFile;

class ThemeConfig
{

    /**
     * @var mixed[]
     */
    private $options;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var ThemeConfigOptionsResolver
     */
    private $themeConfigOptionsResolver;


    public function __construct(string $filePath, ThemeConfigOptionsResolver $themeConfigOptionsResolver)
    {
        if (! is_file($filePath)) {
            throw new ConfigurationException(sprintf(
                'File "%s" not found.',
                $filePath
            ));
        }
        $this->filePath = $filePath;
        $this->themeConfigOptionsResolver = $themeConfigOptionsResolver;
    }


    /**
     * @return mixed[]
     */
    public function getOptions(): array
    {
        if ($this->options === null) {
            $file = new NeonFile($this->filePath);
            $values = $file->read();
            $values['templatesPath'] = dirname($this->filePath);
            $this->options = $this->themeConfigOptionsResolver->resolve($values);
        }
        return $this->options;
    }
}
