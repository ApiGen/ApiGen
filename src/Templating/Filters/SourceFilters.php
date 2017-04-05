<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\InClassInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\LinedInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;

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
     * @param ElementReflectionInterface $element
     * @param bool $withLine Include file line number into the link
     */
    public function sourceUrl(ElementReflectionInterface $element, bool $withLine = true): string
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

        $url = sprintf($this->configuration->getOption('template')['templates']['source']['filename'], $file);
        if ($withLine) {
            $url .= $this->getElementLinesAnchor($element);
        }

        return $url;
    }

    private function isDirectUrl(ElementReflectionInterface $element): bool
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
