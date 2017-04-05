<?php declare(strict_types=1);

namespace ApiGen\Configuration\Theme;

use ApiGen\Configuration\Exceptions\ConfigurationException;
use ApiGen\Configuration\OptionsResolverFactory;
use Nette;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ThemeConfigOptionsResolver extends Nette\Object
{
    /**
     * @var mixed[]
     */
    private $defaults = [
        'name' => '',
        ThemeConfigOptions::RESOURCES => [
            'resources' => 'resources'
        ],
        ThemeConfigOptions::TEMPLATES => [
            ThemeConfigOptions::OVERVIEW => [
                'filename' => 'index.html',
                'template' => 'overview.latte'
            ],
            ThemeConfigOptions::COMBINED => [
                'filename' => 'resources/combined.js',
                'template' => 'combined.js.latte'
            ],
            ThemeConfigOptions::ELEMENT_LIST => [
                'filename' => 'elementlist.js',
                'template' => 'elementlist.js.latte'
            ],
            ThemeConfigOptions::T_NAMESPACE => [
                'filename' => 'namespace-%s.html',
                'template' => 'namespace.latte'
            ],
            ThemeConfigOptions::T_CLASS => [
                'filename' => 'class-%s.html',
                'template' => 'class.latte'
            ],
            ThemeConfigOptions::T_FUNCTION => [
                'filename' => 'function-%s.html',
                'template' => 'function.latte'
            ],
            ThemeConfigOptions::ANNOTATION_GROUP => [
                'filename' => 'annotation-group-%s.html',
                'template' => 'annotation-group.latte'
            ],
            ThemeConfigOptions::SOURCE => [
                'filename' => 'source-%s.html',
                'template' => 'source.latte'
            ]
        ],
        ThemeConfigOptions::TEMPLATES_PATH => ''
    ];

    /**
     * @var OptionsResolver
     */
    private $resolver;

    /**
     * @var OptionsResolverFactory
     */
    private $optionsResolverFactory;

    public function __construct(OptionsResolverFactory $optionsResolverFactory)
    {
        $this->optionsResolverFactory = $optionsResolverFactory;
    }

    /**
     * @param mixed[] $options
     * @return mixed[]
     */
    public function resolve(array $options): array
    {
        $this->resolver = $this->optionsResolverFactory->create();
        $this->setDefaults();
        $this->setNormalizers();

        return $this->resolver->resolve($options);
    }

    private function setDefaults(): void
    {
        $this->resolver->setDefaults($this->defaults);
    }

    private function setNormalizers(): void
    {
        $this->resolver->setNormalizer(ThemeConfigOptions::TEMPLATES, function (Options $options, $value) {
            return $this->makeTemplatePathsAbsolute($value, $options);
        });

        $this->resolver->setNormalizer(ThemeConfigOptions::RESOURCES, function (Options $options, $resources) {
            $absolutizedResources = [];
            foreach ($resources as $key => $resource) {
                $key = $options['templatesPath'] . '/' . $key;
                $absolutizedResources[$key] = $resource;
            }

            return $absolutizedResources;
        });
    }

    /**
     * @param string[] $value
     * @param Options $options
     * @return string[]
     */
    private function makeTemplatePathsAbsolute(array $value, Options $options): array
    {
        foreach ($value as $type => $settings) {
            $filePath = $options[ThemeConfigOptions::TEMPLATES_PATH] . '/' . $settings['template'];
            $value[$type]['template'] = $filePath;
            $this->validateFileExistence($filePath, $type);
        }

        return $value;
    }

    private function validateFileExistence(string $file, string $type): void
    {
        if (! is_file($file)) {
            throw new ConfigurationException(sprintf(
                'Template for "%s" was not found in "%s"',
                $type,
                $file
            ));
        }
    }
}
