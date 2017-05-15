<?php declare(strict_types=1);

namespace ApiGen\StringRouting\Route;

use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\StringRouting\Contract\Route\RouteInterface;

final class SourceCodeRoute implements RouteInterface
{
    public function match(string $name): bool
    {
        return $name === 'sourceCode';
    }

    /**
     * @param AbstractReflectionInterface $reflection
     */
    public function constructUrl($reflection): string
    {
        if ($reflection instanceof ClassReflectionInterface) {
            return 'source-class-' . $reflection->getName() . '.html';
        }

        if ($reflection instanceof TraitReflectionInterface) {
            return 'source-trait-' . $reflection->getName() . '.html';
        }

        if ($reflection instanceof InterfaceReflectionInterface) {
            return 'source-interface-' . $reflection->getName() . '.html';
        }

        return '...';
    }


//
//    /**
//     * @var ConfigurationInterface
//     */
//    private $configuration;
//
//    /**
//     * @var callable[]
//     */
//    private $reflectionToCallbackMap = [];
//
//    public function __construct(ConfigurationInterface $configuration)
//    {
//        $this->configuration = $configuration;
//
//        $this->reflectionToCallbackMap[ClassReflectionInterface::class] = function (ClassReflectionInterface $classReflection) {
//            return 'source-class-' . Strings::webalize($classReflection->getName()) . 'html';
//        };
//        $this->reflectionToCallbackMap[TraitReflectionInterface::class] = function (TraitReflectionInterface $classReflection) {
//            return 'source-trait-' . Strings::webalize($classReflection->getName()) . 'html';
//        };
//        $this->reflectionToCallbackMap[InterfaceReflectionInterface::class] = function (InterfaceReflectionInterface $classReflection) {
//            return 'source-in
//            terface-' . Strings::webalize($classReflection->getName()) . 'html';
//        };
//    }
//
//    /**
//     * @return callable[]
//     */
//    public function getFilters(): array
//    {
//        return [
//            'staticFile' => function (string $filename): string {
//                return $this->staticFile($filename);
//            },
//            'sourceUrl' => function (AbstractReflectionInterface $reflection): string {
//                return $this->sourceUrl($reflection);
//            },
//            'sourceUrlWithLine' => function (StartAndEndLineInterface $reflection): string {
//                return $this->sourceUrl($reflection) . $this->getElementLinesAnchor($reflection);
//            }
//        ];
//    }
//
//    private function staticFile(string $filename): string
//    {
//        $filename = $this->configuration->getOption(DestinationOption::NAME) . '/' . $filename;
//        if (is_file($filename)) {
//            $filename .= '?' . md5_file($filename);
//        }
//
//        return $filename;
//    }
//
//    /**
//     * @param AbstractReflectionInterface|StartAndEndLineInterface $reflection
//     */
//    private function sourceUrl(AbstractReflectionInterface $reflection): string
//    {
//        foreach ($this->reflectionToCallbackMap as $reflectionInterface => $sourceUrlCallback) {
//            if ($reflection instanceof $reflectionInterface) {
//                return $sourceUrlCallback($reflection);
//            }
//        }
//
//        return '';
//        // reflection map => output
//
//        $relativeUrl = 'source-';
//
//            if ($reflection instanceof ClassReflectionInterface) {
//                $relativeUrl .= 'class-' . Strings::webalize($reflection->getName());
//            } elseif ($reflection instanceof FunctionReflectionInterface) {
//                $relativeUrl .= 'function-' . Strings::webalize($reflection->getName());
//            }
//        } elseif ($reflection instanceof InClassInterface) {
//            $relativeUrl .= 'class-' . Strings::webalize($reflection->getDeclaringClassName());
//        }
//
//        return $relativeUrl .= '.html';
//    }
//    private function getElementLinesAnchor(StartAndEndLineInterface $element): string
//    {
//        $anchor = '#' . $element->getStartLine();
//        if ($element->getStartLine() !== $element->getEndLine()) {
//            $anchor .= '-' . $element->getEndLine();
//        }
//
//        return $anchor;
//    }
}
