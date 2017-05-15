<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\ModularConfiguration\Option\DestinationOption;
use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\StartAndEndLineInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\Helper\ReflectionAnalyzer;
use Nette\Utils\Strings;
use Symplify\ModularLatteFilters\Contract\DI\LatteFiltersProviderInterface;

final class SourceFilters implements LatteFiltersProviderInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var callable[]
     */
    private $reflectionToCallbackMap = [];

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;

        $this->reflectionToCallbackMap[ClassReflectionInterface::class] = function (ClassReflectionInterface $classReflection) {
            return 'source-class-' . Strings::webalize($classReflection->getName()) . 'html';
        };
        $this->reflectionToCallbackMap[TraitReflectionInterface::class] = function (TraitReflectionInterface $classReflection) {
            return 'source-trait-' . Strings::webalize($classReflection->getName()) . 'html';
        };
        $this->reflectionToCallbackMap[InterfaceReflectionInterface::class] = function (InterfaceReflectionInterface $classReflection) {
            return 'source-interface-' . Strings::webalize($classReflection->getName()) . 'html';
        };
    }

    /**
     * @return callable[]
     */
    public function getFilters(): array
    {
        return [
            'staticFile' => function (string $filename): string {
                return $this->staticFile($filename);
            },
            'sourceUrl' => function (AbstractReflectionInterface $reflection): string {
                return $this->sourceUrl($reflection);
            },
            'sourceUrlWithLine' => function (StartAndEndLineInterface $reflection): string {
                return $this->sourceUrl($reflection) . $this->getElementLinesAnchor($reflection);
            }
        ];
    }

    private function staticFile(string $filename): string
    {
        $filename = $this->configuration->getOption(DestinationOption::NAME) . '/' . $filename;
        if (is_file($filename)) {
            $filename .= '?' . md5_file($filename);
        }

        return $filename;
    }

    /**
     * @param AbstractReflectionInterface|StartAndEndLineInterface $reflection
     */
    private function sourceUrl(AbstractReflectionInterface $reflection): string
    {
        $reflectionInterface = ReflectionAnalyzer::getReflectionInterfaceFromReflection($reflection);

        return $this->reflectionToCallbackMap[$reflectionInterface]($reflection);
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
    }

    private function getElementLinesAnchor(StartAndEndLineInterface $element): string
    {
        $anchor = '#' . $element->getStartLine();
        if ($element->getStartLine() !== $element->getEndLine()) {
            $anchor .= '-' . $element->getEndLine();
        }

        return $anchor;
    }
}
