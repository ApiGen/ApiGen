<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\NamedDestinationGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Templating\Filters\UrlFilters;

final class ClassGenerator implements NamedDestinationGeneratorInterface
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
        foreach ($this->elementStorage->getClasses() as $classReflection) {
            $this->generateForClass($classReflection);
        }
    }

    public function getDestinationPath(string $className): string
    {
        return $this->configuration->getDestination()
            . DIRECTORY_SEPARATOR
            . sprintf(
                'class-%s.html',
                UrlFilters::urlize($className)
            );
    }

    private function generateForClass(ClassReflectionInterface $classReflection): void
    {
        $template = $this->templateFactory->create();
        $template->setFile($this->getTemplateFile());

        $template->save($this->getDestinationPath($classReflection->getName()), [
            'class' => $classReflection,
            'tree' => array_merge(array_reverse($classReflection->getParentClasses()), [$classReflection]),
        ]);
    }

    private function getTemplateFile(): string
    {
        return $this->configuration->getTemplatesDirectory()
            . DIRECTORY_SEPARATOR
            . 'class.latte';
    }
}
