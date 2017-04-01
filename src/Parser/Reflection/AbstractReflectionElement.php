<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\Behavior\InClassInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use TokenReflection\ReflectionAnnotation;
use TokenReflection\ReflectionClass;
use TokenReflection\ReflectionConstant;
use TokenReflection\ReflectionFunction;

abstract class AbstractReflectionElement extends AbstractReflection implements ElementReflectionInterface
{
    /**
     * @var bool
     */
    protected $isDocumented;

    /**
     * @var mixed[]
     */
    protected $annotations;

    public function isDocumented(): bool
    {
        if ($this->isDocumented === null) {
            $this->isDocumented = $this->reflection->isTokenized() || $this->reflection->isInternal();

            if ($this->isDocumented) {
                if ($this->reflection->isInternal()) {
                    $this->isDocumented = false;
                } elseif ($this->reflection->hasAnnotation('internal')) {
                    $this->isDocumented = false;
                }
            }
        }

        return $this->isDocumented;
    }

    public function isDeprecated(): bool
    {
        if ($this->reflection->isDeprecated()) {
            return true;
        }

        if ($this instanceof InClassInterface) {
            $class = $this->getDeclaringClass();
            return !is_null($class) && $class->isDeprecated();
        }

        return false;
    }

    public function getNamespaceName(): string
    {
        static $namespaces = [];

        $namespaceName = $this->reflection->getNamespaceName();

        if (! $namespaceName) {
            return $namespaceName;
        }

        $lowerNamespaceName = strtolower($namespaceName);
        if (! isset($namespaces[$lowerNamespaceName])) {
            $namespaces[$lowerNamespaceName] = $namespaceName;
        }

        return $namespaces[$lowerNamespaceName];
    }

    public function getPseudoNamespaceName(): string
    {
        return $this->isInternal() ? 'PHP' : $this->getNamespaceName() ?: 'None';
    }

    /**
     * @return string[]
     */
    public function getNamespaceAliases(): array
    {
        return $this->reflection->getNamespaceAliases();
    }

    public function getDescription(): string
    {
        $short = $this->getShortDescription();
        $long = $this->reflection->getAnnotation(ReflectionAnnotation::LONG_DESCRIPTION);

        if (! empty($long)) {
            $short .= "\n\n" . $long;
        }

        return $short;
    }

    /**
     * @return mixed[]
     */
    public function getAnnotations(): array
    {
        if ($this->annotations === null) {
            $annotations = $this->reflection->getAnnotations();
            $annotations = array_change_key_case($annotations, CASE_LOWER);

            unset($annotations[ReflectionAnnotation::SHORT_DESCRIPTION]);
            unset($annotations[ReflectionAnnotation::LONG_DESCRIPTION]);

            $annotations += $this->getAnnotationsFromReflection($this->reflection);
            $this->annotations = $annotations;
        }

        return $this->annotations;
    }

    /**
     * @return mixed[]
     */
    public function getAnnotation(string $name): array
    {
        return $this->hasAnnotation($name) ? $this->getAnnotations()[$name] : [];
    }

    public function hasAnnotation(string $name): bool
    {
        return isset($this->getAnnotations()[$name]);
    }

    /**
     * @param mixed $reflection
     * @return mixed[]
     */
    private function getAnnotationsFromReflection($reflection): array
    {
        $fileLevel = [
            'package' => true,
            'subpackage' => true,
            'author' => true,
            'license' => true,
            'copyright' => true
        ];

        $annotations = [];
        if ($reflection instanceof ReflectionClass || $reflection instanceof ReflectionFunction
            || ($reflection instanceof ReflectionConstant  && $reflection->getDeclaringClassName() === '')
        ) {
            foreach ($reflection->getFileReflection()->getAnnotations() as $name => $value) {
                if (isset($fileLevel[$name]) && empty($annotations[$name])) {
                    $annotations[$name] = $value;
                }
            }
        }

        return $annotations;
    }

    private function getShortDescription(): string
    {
        $short = $this->reflection->getAnnotation(ReflectionAnnotation::SHORT_DESCRIPTION);
        if (! empty($short)) {
            return $short;
        }

        if ($this instanceof ReflectionProperty || $this instanceof ReflectionConstant) {
            $var = $this->getAnnotation('var');
            [, $short] = preg_split('~\s+|$~', $var[0], 2);
        }

        return (string) $short;
    }
}
