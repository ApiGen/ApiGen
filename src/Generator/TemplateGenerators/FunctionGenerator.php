<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\NamedDestinationGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;

final class FunctionGenerator implements NamedDestinationGeneratorInterface
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
        foreach ($this->elementStorage->getFunctions() as $reflectionFunction) {
            $this->generateForFunction($reflectionFunction);
        }
    }

    public function getDestinationPath(string $functionName): string
    {
        return $this->configuration->getDestinationForFileMaskAndName(
            'function-%s',
            $functionName
        );
    }

    private function generateForFunction(FunctionReflectionInterface $reflectionFunction): void
    {
        $template = $this->templateFactory->create();
        $template->setFile($this->getTemplateFile());

        $template->save($this->getDestinationPath($reflectionFunction->getName()), [
            'function' => $reflectionFunction
        ]);
    }

    private function getTemplateFile(): string
    {
        return $this->configuration->getTemplateByName('function');
    }
}
