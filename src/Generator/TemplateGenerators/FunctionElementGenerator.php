<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Generator\Event\GenerateProgressEvent;
use ApiGen\Generator\Event\GeneratorEvents;
use ApiGen\Templating\Template;

class FunctionElementGenerator extends AbstractElementGenerator
{

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        foreach ($this->elementStorage->getFunctions() as $name => $reflectionFunction) {
            $template = $this->templateFactory->createForReflection($reflectionFunction);
            $template = $this->loadTemplateWithParameters($template, $reflectionFunction);
            $template->save();

            $this->eventDispatcher->dispatch(new GenerateProgressEvent(GeneratorEvents::ON_GENERATE_PROGRESS));
        }
    }


    /**
     * @return int
     */
    public function getStepCount()
    {
        return count($this->elementStorage->getFunctions());
    }


    /**
     * @return Template
     */
    private function loadTemplateWithParameters(Template $template, FunctionReflectionInterface $function)
    {
        $template = $this->namespaceAndPackageLoader->loadTemplateWithElementNamespaceOrPackage($template, $function);
        $template->setParameters([
            'function' => $function
        ]);
        return $template;
    }
}
