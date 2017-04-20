<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Templating\Filters\UrlFilters;

final class TraitGenerator implements TemplateGeneratorInterface
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
        foreach ($this->elementStorage->getTraits() as $name => $traitReflection) {
            $template = $this->templateFactory->createForReflection($traitReflection);

            // $template->setPath()

            $template->save($this->getDestinationPath($traitReflection), [
                'trait' => $traitReflection,
                'tree' => array_merge(array_reverse($traitReflection->getParentClasses()), [$traitReflection]),
            ]);
        }
    }

    private function getDestinationPath(ClassReflectionInterface $traitReflection): string
    {
        return $this->configuration->getDestination()
            . DIRECTORY_SEPARATOR
            . sprintf(
                'trait-%s.html',
                UrlFilters::urlize($traitReflection->getName())
            );
    }
}
