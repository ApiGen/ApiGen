<?php declare(strict_types=1);

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Generator\Event\GenerateProgressEvent;
use ApiGen\Templating\Template;

class ClassElementGenerator extends AbstractElementGenerator
{

    public function generate(): void
    {
        foreach ($this->elementStorage->getClassElements() as $name => $reflectionClass) {
            $template = $this->templateFactory->createForReflection($reflectionClass);
            $template = $this->loadTemplateWithParameters($template, $reflectionClass);
            $template->save();

            $this->eventDispatcher->dispatch(GenerateProgressEvent::class);
        }
    }


    public function getStepCount(): int
    {
        return count($this->elementStorage->getClassElements());
    }


    private function loadTemplateWithParameters(Template $template, ClassReflectionInterface $class): Template
    {
        $template = $this->namespaceLoader->loadTemplateWithElementNamespace($template, $class);
        $template->setParameters([
            'class' => $class,
            'tree' => array_merge(array_reverse($class->getParentClasses()), [$class]),
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
