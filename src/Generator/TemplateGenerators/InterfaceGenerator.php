<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Templating\Filters\UrlFilters;

final class InterfaceGenerator implements TemplateGeneratorInterface
{
    /**
     * @var TemplateFactoryInterface
     */
    private $templateFactory;

    /**
     * @var ElementStorageInterface
     */
    private $elementStorage;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(
        TemplateFactoryInterface $templateFactory,
        ElementStorageInterface $elementStorage,
        ConfigurationInterface $configuration
    ) {
        $this->templateFactory = $templateFactory;
        $this->elementStorage = $elementStorage;
        $this->configuration = $configuration;
    }

    public function generate(): void
    {
        foreach ($this->elementStorage->getInterfaces() as $name => $interfaceReflection) {
            $template = $this->templateFactory->createForReflection($interfaceReflection);

            $template->save($this->getDestinationPath($interfaceReflection), [
                'interface' => $interfaceReflection,
                'tree' => array_merge(array_reverse($interfaceReflection->getParentClasses()), [$interfaceReflection]),
            ]);
        }
    }

    private function getDestinationPath(ClassReflectionInterface $interfaceReflection): string
    {
        $fileName = sprintf(
            'interface-%s.html',
            UrlFilters::urlize($interfaceReflection->getName())
        );

        return $this->configuration->getDestination()
            . DIRECTORY_SEPARATOR
            . $fileName;
    }
}
