<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Generator\Event\GenerateProgressEvent;
use ApiGen\Templating\Template;

class ConstantElementGenerator extends AbstractElementGenerator
{
    public function generate(): void
    {
        foreach ($this->elementStorage->getConstants() as $name => $reflectionConstant) {
            $template = $this->templateFactory->createForReflection($reflectionConstant);
            $template = $this->loadTemplateWithParameters($template, $reflectionConstant);
            $template->save();

            $this->eventDispatcher->dispatch(GenerateProgressEvent::class);
        }
    }

    public function getStepCount(): int
    {
        return count($this->elementStorage->getConstants());
    }

    private function loadTemplateWithParameters(Template $template, ConstantReflectionInterface $constant): Template
    {
        $template = $this->namespaceLoader->loadTemplateWithElementNamespace($template, $constant);
        $template->setParameters([
            'constant' => $constant
        ]);
        return $template;
    }
}
