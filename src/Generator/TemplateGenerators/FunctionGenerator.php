<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Templating\TemplateFactory\TemplateFactoryInterface;
use ApiGen\Templating\Filters\Filters;

final class FunctionGenerator implements TemplateGeneratorInterface
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

    private function getTemplateFile(): string
    {
        return $this->configuration->getTemplatesDirectory()
            . DIRECTORY_SEPARATOR
            . 'function.latte';
    }

    private function getDestination(FunctionReflectionInterface $reflectionFunction): string
    {
        return $this->configuration->getDestination()
            . DIRECTORY_SEPARATOR
            . sprintf(
                'function-%s.html',
                Filters::urlize($reflectionFunction->getName())
            );
    }

    private function generateForFunction(FunctionReflectionInterface $reflectionFunction): void
    {
        $template = $this->templateFactory->createForReflection($reflectionFunction);
        $template->setFile($this->getTemplateFile());
        $template->save($this->getDestination($reflectionFunction), [
            'function' => $reflectionFunction
        ]);
    }
}
