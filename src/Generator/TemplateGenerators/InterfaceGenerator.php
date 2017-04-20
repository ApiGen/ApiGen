<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\NamedDestinationGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;

final class InterfaceGenerator implements NamedDestinationGeneratorInterface
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
        foreach ($this->elementStorage->getInterfaces() as $interfaceReflection) {
            $this->generateForInterface($interfaceReflection);
        }
    }

    public function getDestinationPath(string $interfaceName): string
    {
        return $this->configuration->getDestinationForFileMaskAndName(
            'interface-%s',
            $interfaceName
        );
    }

    private function generateForInterface(ClassReflectionInterface $interfaceReflection): void
    {
        $template = $this->templateFactory->create();
        $template->setFile($this->getTemplateFile());

        $template->save($this->getDestinationPath($interfaceReflection->getName()), [
            'interface' => $interfaceReflection,
            'tree' => array_merge(array_reverse($interfaceReflection->getParentClasses()), [$interfaceReflection]),
        ]);
    }

    private function getTemplateFile(): string
    {
        return $this->configuration->getTemplateByName('interface');
    }
}
