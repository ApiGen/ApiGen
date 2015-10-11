<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\Behavior\InClassInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use TokenReflection;
use TokenReflection\Exception\BaseException;
use TokenReflection\ReflectionAnnotation;
use TokenReflection\ReflectionClass;
use TokenReflection\ReflectionConstant;
use TokenReflection\ReflectionFunction;

abstract class ReflectionElement extends ReflectionBase implements ElementReflectionInterface
{

    /**
     * @var bool
     */
    protected $isDocumented;

    /**
     * @var array
     */
    protected $annotations;

    /**
     * Reasons why this element's reflection is invalid.
     *
     * @var array
     */
    private $reasons = [];


    /**
     * @return ReflectionExtension|NULL
     */
    public function getExtension()
    {
        $extension = $this->reflection->getExtension();
        return $extension === null ? null : $this->reflectionFactory->createFromReflection($extension);
    }


    /**
     * @return bool
     */
    public function getExtensionName()
    {
        return $this->reflection->getExtensionName();
    }


    /**
     * {@inheritdoc}
     */
    public function getStartPosition()
    {
        return $this->reflection->getStartPosition();
    }


    /**
     * {@inheritdoc}
     */
    public function getEndPosition()
    {
        return $this->reflection->getEndPosition();
    }


    /**
     * {@inheritdoc}
     */
    public function isMain()
    {
        $main = $this->configuration->getMain();
        return empty($main) || strpos($this->getName(), $main) === 0;
    }


    /**
     * {@inheritdoc}
     */
    public function isDocumented()
    {
        if ($this->isDocumented === null) {
            $this->isDocumented = $this->reflection->isTokenized() || $this->reflection->isInternal();

            if ($this->isDocumented) {
                $php = $this->configuration->isPhpCoreDocumented();
                $internal = $this->configuration->isInternalDocumented();

                if (! $php && $this->reflection->isInternal()) {
                    $this->isDocumented = false;

                } elseif (! $internal && ($internal = $this->reflection->getAnnotation('internal'))
                    && empty($internal[0])
                ) {
                    $this->isDocumented = false;

                } elseif ($this->reflection->hasAnnotation('ignore')) {
                    $this->isDocumented = false;
                }
            }
        }

        return $this->isDocumented;
    }


    /**
     * {@inheritdoc}
     */
    public function isDeprecated()
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


    /**
     * {@inheritdoc}
     */
    public function inPackage()
    {
        return ($this->getPackageName() !== '');
    }


    /**
     * {@inheritdoc}
     */
    public function getPackageName()
    {
        static $packages = [];

        if ($package = $this->getAnnotation('package')) {
            $packageName = preg_replace('~\s+.*~s', '', $package[0]);
            if (empty($packageName)) {
                return '';
            }

            if ($subpackage = $this->getAnnotation('subpackage')) {
                $subpackageName = preg_replace('~\s+.*~s', '', $subpackage[0]);
                if (! empty($subpackageName) && strpos($subpackageName, $packageName) === 0) {
                    $packageName = $subpackageName;

                } else {
                    $packageName .= '\\' . $subpackageName;
                }
            }
            $packageName = strtr($packageName, '._/', '\\\\\\');

            $lowerPackageName = strtolower($packageName);
            if (! isset($packages[$lowerPackageName])) {
                $packages[$lowerPackageName] = $packageName;
            }

            return $packages[$lowerPackageName];
        }

        return '';
    }


    /**
     * {@inheritdoc}
     */
    public function getPseudoPackageName()
    {
        if ($this->isInternal()) {
            return 'PHP';
        }

        return $this->getPackageName() ?: 'None';
    }


    /**
     * {@inheritdoc}
     */
    public function inNamespace()
    {
        return $this->getNamespaceName() !== '';
    }


    /**
     * {@inheritdoc}
     */
    public function getNamespaceName()
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


    /**
     * {@inheritdoc}
     */
    public function getPseudoNamespaceName()
    {
        return $this->isInternal() ? 'PHP' : $this->getNamespaceName() ?: 'None';
    }


    /**
     * {@inheritdoc}
     */
    public function getNamespaceAliases()
    {
        return $this->reflection->getNamespaceAliases();
    }


    /**
     * {@inheritdoc}
     */
    public function getShortDescription()
    {
        $short = $this->reflection->getAnnotation(ReflectionAnnotation::SHORT_DESCRIPTION);
        if (! empty($short)) {
            return $short;
        }

        if ($this instanceof ReflectionProperty || $this instanceof ReflectionConstant) {
            $var = $this->getAnnotation('var');
            list(, $short) = preg_split('~\s+|$~', $var[0], 2);
        }

        return $short;
    }


    /**
     * {@inheritdoc}
     */
    public function getLongDescription()
    {
        $short = $this->getShortDescription();
        $long = $this->reflection->getAnnotation(ReflectionAnnotation::LONG_DESCRIPTION);

        if (! empty($long)) {
            $short .= "\n\n" . $long;
        }

        return $short;
    }


    /**
     * {@inheritdoc}
     */
    public function getDocComment()
    {
        return $this->reflection->getDocComment();
    }


    /**
     * {@inheritdoc}
     */
    public function getAnnotations()
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
     * {@inheritdoc}
     */
    public function getAnnotation($name)
    {
        return $this->hasAnnotation($name) ? $this->getAnnotations()[$name] : null;
    }


    /**
     * {@inheritdoc}
     */
    public function hasAnnotation($name)
    {
        $annotations = $this->getAnnotations();
        return isset($annotations[$name]);
    }


    /**
     * {@inheritdoc}
     */
    public function addAnnotation($annotation, $value)
    {
        if ($this->annotations === null) {
            $this->getAnnotations();
        }
        $this->annotations[$annotation][] = $value;

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function addReason(BaseException $reason)
    {
        $this->reasons[] = $reason;
        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function getReasons()
    {
        return $this->reasons;
    }


    /**
     * {@inheritdoc}
     */
    public function hasReasons()
    {
        return ! empty($this->reasons);
    }


    /**
     * @param mixed $reflection
     * @return array
     */
    private function getAnnotationsFromReflection($reflection)
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
            || ($reflection instanceof ReflectionConstant  && $reflection->getDeclaringClassName() === null)
        ) {
            foreach ($reflection->getFileReflection()->getAnnotations() as $name => $value) {
                if (isset($fileLevel[$name]) && empty($annotations[$name])) {
                    $annotations[$name] = $value;
                }
            }
        }
        return $annotations;
    }
}
