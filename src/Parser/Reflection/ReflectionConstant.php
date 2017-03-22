<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;

final class ReflectionConstant extends ReflectionElement implements ConstantReflectionInterface
{
    public function getName(): string
    {
        return $this->reflection->getName();
    }


    public function getShortName(): string
    {
        return $this->reflection->getShortName();
    }


    public function getTypeHint(): string
    {
        if ($annotations = $this->getAnnotation('var')) {
            [$types] = preg_split('~\s+|$~', $annotations[0], 2);
            if (! empty($types)) {
                return $types;
            }
        }

        try {
            $type = gettype($this->getValue());
            if (strtolower($type) !== 'null') {
                return $type;
            }
        } catch (\Exception $e) {
            return '';
        }
    }


    public function getDeclaringClass(): ?ClassReflectionInterface
    {
        $className = (string) $this->reflection->getDeclaringClassName();
        return $className === '' ? null : $this->getParsedClasses()[$className];
    }


    public function getDeclaringClassName(): string
    {
        return (string) $this->reflection->getDeclaringClassName();
    }


    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->reflection->getValue();
    }


    public function getValueDefinition(): string
    {
        return $this->reflection->getValueDefinition();
    }
}
