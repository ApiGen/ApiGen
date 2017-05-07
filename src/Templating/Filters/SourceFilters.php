<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Reflection\Contract\Reflection\Behavior\InClassInterface;
use ApiGen\Reflection\Contract\Reflection\Behavior\LinedInterface;
use ApiGen\Reflection\Contract\Reflection\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\FunctionReflectionInterface;

final class SourceFilters extends Filters
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    public function staticFile(string $name): string
    {
        $filename = $this->configuration->getOption('destination') . '/' . $name;
        if (is_file($filename)) {
            $name .= '?' . sha1_file($filename);
        }

        return $name;
    }

    /**
     *
     * @todo split into 2 methods, no bool
     */
    public function sourceUrl(ReflectionInterface $element, bool $withLine = true): string
    {
        $file = '';
        $elementName = '';

        if ($this->isDirectUrl($element)) {
            $elementName = $element->getName();
            if ($element instanceof ClassReflectionInterface) {
                $file = 'class-';
            } elseif ($element instanceof FunctionReflectionInterface) {
                $file = 'function-';
            }
        } elseif ($element instanceof InClassInterface) {
            $elementName = $element->getDeclaringClassName();
            $file = 'class-';
        }

        $file .= self::urlize($elementName);

        $url = sprintf('source-%s.html', $file);
        if ($withLine) {
            $url .= $this->getElementLinesAnchor($element);
        }

        return $url;
    }

    private function isDirectUrl(ReflectionInterface $element): bool
    {
        if ($element instanceof ClassReflectionInterface
            || $element instanceof FunctionReflectionInterface
        ) {
            return true;
        }

        return false;
    }

    private function getElementLinesAnchor(LinedInterface $element): string
    {
        $anchor = '#' . $element->getStartLine();
        if ($element->getStartLine() !== $element->getEndLine()) {
            $anchor .= '-' . $element->getEndLine();
        }

        return $anchor;
    }
}
