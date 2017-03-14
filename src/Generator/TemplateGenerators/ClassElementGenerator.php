<?php

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Generator\Event\GenerateProgressEvent;
use ApiGen\Generator\Event\GeneratorEvents;
use ApiGen\Templating\Template;

class ClassElementGenerator extends AbstractElementGenerator
{

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        foreach ($this->elementStorage->getClassElements() as $name => $reflectionClass) {
            $template = $this->templateFactory->createForReflection($reflectionClass);
            $template = $this->loadTemplateWithParameters($template, $reflectionClass);
            $template->save();

            $this->eventDispatcher->dispatch(new GenerateProgressEvent(GeneratorEvents::ON_GENERATE_PROGRESS));
        }
    }


    /**
     * {@inheritdoc}
     */
    public function getStepCount()
    {
        return count($this->elementStorage->getClassElements());
    }


    /**
     * @return Template
     */
    private function loadTemplateWithParameters(Template $template, ClassReflectionInterface $class)
    {
        $template = $this->namespaceAndPackageLoader->loadTemplateWithElementNamespaceOrPackage($template, $class);
        $template->setParameters([
            'class' => $class,
            'directSubClasses' => $class->getDirectSubClasses(),
            'indirectSubClasses' => $class->getIndirectSubClasses(),
            'directImplementers' => $class->getDirectImplementers(),
            'indirectImplementers' => $class->getIndirectImplementers(),
            'directUsers' => $class->getDirectUsers(),
            'indirectUsers' => $class->getIndirectUsers(),
        ]);
        return $template;
    }
}
