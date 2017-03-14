<?php

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Generator\Event\GenerateProgressEvent;
use ApiGen\Generator\Event\GeneratorEvents;
use ApiGen\Templating\Template;

class ConstantElementGenerator extends AbstractElementGenerator
{

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        foreach ($this->elementStorage->getConstants() as $name => $reflectionConstant) {
            $template = $this->templateFactory->createForReflection($reflectionConstant);
            $template = $this->loadTemplateWithParameters($template, $reflectionConstant);
            $template->save();

            $this->eventDispatcher->dispatch(new GenerateProgressEvent(GeneratorEvents::ON_GENERATE_PROGRESS));
        }
    }


    /**
     * {@inheritdoc}
     */
    public function getStepCount()
    {
        return count($this->elementStorage->getConstants());
    }


    /**
     * @return Template
     */
    private function loadTemplateWithParameters(Template $template, ConstantReflectionInterface $constant)
    {
        $template = $this->namespaceLoader->loadTemplateWithElementNamespace($template, $constant);
        $template->setParameters([
            'constant' => $constant
        ]);
        return $template;
    }
}
