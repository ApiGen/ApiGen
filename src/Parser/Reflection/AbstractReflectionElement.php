<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Contracts\Parser\Reflection\Behavior\InClassInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use TokenReflection\ReflectionAnnotation;
use TokenReflection\ReflectionConstant;

abstract class AbstractReflectionElement extends AbstractReflection implements ElementReflectionInterface
{
    /**
     * @var mixed[]
     */
    protected $annotations;

    public function isDocumented(): bool
    {
        return ! $this->reflection->isInternal();
    }

    public function isDeprecated(): bool
    {
        if ($this->reflection->isDeprecated()) {
            return true;
        }

        if ($this instanceof InClassInterface) {
            $class = $this->getDeclaringClass();
            return $class !== null && $class->isDeprecated();
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

    private function getShortDescription(): string
    {
        $short = $this->reflection->getAnnotation(ReflectionAnnotation::SHORT_DESCRIPTION);
        if (! empty($short)) {
            return $short;
        }

        if ($this instanceof ReflectionProperty || $this instanceof ReflectionConstant) {
            $var = $this->getAnnotation(AnnotationList::VAR_);
            [, $short] = preg_split('~\s+|$~', $var[0], 2);
        }

        return (string) $short;
    }
}
