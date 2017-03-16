<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Generator\Event\GenerateProgressEvent;
use ApiGen\Templating\Template;

class FunctionElementGenerator extends AbstractElementGenerator
{

    public function generate(): void
    {
        foreach ($this->elementStorage->getFunctions() as $name => $reflectionFunction) {
            $template = $this->templateFactory->createForReflection($reflectionFunction);
            $template = $this->loadTemplateWithParameters($template, $reflectionFunction);
            $template->save();

            $this->eventDispatcher->dispatch(GenerateProgressEvent::class);
        }
    }


    public function getStepCount(): int
    {
        return count($this->elementStorage->getFunctions());
    }


    private function loadTemplateWithParameters(Template $template, FunctionReflectionInterface $function): Template
    {
        $template = $this->namespaceLoader->loadTemplateWithElementNamespace($template, $function);
        $template->setParameters([
            'function' => $function
        ]);
        return $template;
    }
}
