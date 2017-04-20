<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\NamedDestinationGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Templating\Filters\UrlFilters;

final class TraitGenerator implements NamedDestinationGeneratorInterface
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
        foreach ($this->elementStorage->getTraits() as $traitReflection) {
            $this->generateForTrait($traitReflection);
        }
    }

    public function getDestinationPath(string $traitName): string
    {
        return $this->configuration->getDestination()
            . DIRECTORY_SEPARATOR
            . sprintf(
                'trait-%s.html',
                UrlFilters::urlize($traitName)
            );
    }

    private function generateForTrait(ClassReflectionInterface $traitReflection): void
    {
        $template = $this->templateFactory->create();
        $template->setFile($this->getTemplateFile());

        $template->save($this->getDestinationPath($traitReflection->getName()), [
            'trait' => $traitReflection,
            'tree' => array_merge(array_reverse($traitReflection->getParentClasses()), [$traitReflection]),
        ]);
    }

    private function getTemplateFile(): string
    {
        return $this->configuration->getTemplatesDirectory()
            . DIRECTORY_SEPARATOR
            . 'trait.latte';
    }
}
