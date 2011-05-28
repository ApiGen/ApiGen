<?php

/**
 * ApiGen - API Generator.
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011 Ondřej Nešpor (http://andrewsville.cz)
 * Copyright (c) 2011 Jaroslav Hanslík (http://kukulich.cz)
 *
 * This source file is subject to the "Nette license", and/or
 * GPL license. For more information please see http://nette.org
 */

namespace Apigen;

use Nette;
use TokenReflection\IReflectionProperty as ReflectionProperty, TokenReflection\IReflectionMethod as ReflectionMethod, TokenReflection\IReflectionParameter as ReflectionParameter;
use TokenReflection\IReflectionExtension as ReflectionExtension, TokenReflection\ReflectionAnnotation;

/**
 * Customized ApiGen template class.
 *
 * Adds ApiGen helpers to the Nette\Templating\FileTemplate parent class.
 *
 * @author Jaroslav Hanslík
 * @author Ondřej Nešpor
 */
class Template extends Nette\Templating\FileTemplate
{
	/**
	 * Config.
	 *
	 * @var \Apigen\Config
	 */
	private $config;

	/**
	 * List of classes.
	 *
	 * @var \ArrayObject
	 */
	private $classes;

	/**
	 * List of constants.
	 *
	 * @var \ArrayObject
	 */
	private $constants;

	/**
	 * List of functions.
	 *
	 * @var \ArrayObject
	 */
	private $functions;

	/**
	 * Creates template.
	 *
	 * @param \Apigen\Generator $generator
	 */
	public function __construct(Generator $generator)
	{
		$this->config = $generator->getConfig();
		$this->classes = $generator->getClasses();
		$this->constants = $generator->getConstants();
		$this->functions = $generator->getFunctions();

		$that = $this;

		$latte = new Nette\Latte\Engine();
		$macroSet = new Nette\Latte\Macros\MacroSet($latte->parser);
		$macroSet->addMacro('try', 'try {', '} catch (\Exception $e) {}');
		$this->registerFilter($latte);

		// Common operations
		$this->registerHelperLoader('Nette\Templating\DefaultHelpers::loader');
		$this->registerHelper('ucfirst', 'ucfirst');
		$this->registerHelper('replaceRE', 'Nette\Utils\Strings::replace');

		// PHP source highlight
		$fshl = new \fshlParser('HTML_UTF8');
		$this->registerHelper('highlightPHP', function($source) use ($fshl) {
			return $fshl->highlightString('PHP', (string) $source);
		});
		$this->registerHelper('highlightValue', function($definition) use ($that) {
			return $that->highlightPHP(preg_replace('~^(?:[ ]{4}|\t)~m', '', $definition));
		});

		// Urls
		$this->registerHelper('packageUrl', new Nette\Callback($this, 'getPackageUrl'));
		$this->registerHelper('namespaceUrl', new Nette\Callback($this, 'getNamespaceUrl'));
		$this->registerHelper('classUrl', new Nette\Callback($this, 'getClassUrl'));
		$this->registerHelper('methodUrl', new Nette\Callback($this, 'getMethodUrl'));
		$this->registerHelper('propertyUrl', new Nette\Callback($this, 'getPropertyUrl'));
		$this->registerHelper('constantUrl', new Nette\Callback($this, 'getConstantUrl'));
		$this->registerHelper('functionUrl', new Nette\Callback($this, 'getFunctionUrl'));
		$this->registerHelper('sourceUrl', new Nette\Callback($this, 'getSourceUrl'));
		$this->registerHelper('manualUrl', new Nette\Callback($this, 'getManualUrl'));

		// Packages
		$this->registerHelper('packageName', function($packageName) {
			if ($pos = strpos($packageName, '\\')) {
				return substr($packageName, 0, $pos);
			}
			return $packageName;
		});
		$this->registerHelper('subpackageName', function($packageName) {
			if ($pos = strpos($packageName, '\\')) {
				return substr($packageName, $pos + 1);
			}
			return '';
		});

		// Namespaces
		$this->registerHelper('namespaceLinks', new Nette\Callback($this, 'getNamespaceLinks'));
		$this->registerHelper('subnamespaceName', function($namespaceName) {
			if ($pos = strrpos($namespaceName, '\\')) {
				return substr($namespaceName, $pos + 1);
			}
			return $namespaceName;
		});

		// Texy
		$texy = new \Texy();
		$linkModule = new \TexyLinkModule($texy);
		$linkModule->shorten = false;
		$texy->linkModule = $linkModule;
		$texy->mergeLines = false;
		$texy->allowedTags = array_flip($this->config->allowedHtml);
		$texy->allowed['list/definition'] = false;
		$texy->allowed['phrase/em-alt'] = false;
		$texy->allowed['longwords'] = false;
		// Highlighting <code>, <pre>
		$texy->registerBlockPattern(
			function($parser, $matches, $name) use ($fshl) {
				$content = 'code' === $matches[1] ? $fshl->highlightString('PHP', $matches[2]) : htmlSpecialChars($matches[2]);
				$content = $parser->getTexy()->protect($content, \Texy::CONTENT_BLOCK);
				return \TexyHtml::el('pre', $content);
			},
			'~<(code|pre)>(.+?)</\1>~s',
			'codeBlockSyntax'
		);

		// Types
		$this->registerHelper('typeLinks', new Nette\Callback($this, 'getTypeLinks'));
		$this->registerHelper('type', function($value) use ($that) {
			$type = $that->getTypeName(gettype($value));
			return 'null' !== $type ? $type : '';
		});

		// Documentation formatting
		$this->registerHelper('docline', function($text, $context) use ($that, $texy) {
			return $that->resolveLinks($texy->processLine($text), $context);
		});
		$this->registerHelper('docblock', function($text, $context) use ($that, $texy) {
			return $that->resolveLinks($texy->process($text), $context);
		});

		// Docblock descriptions
		$this->registerHelper('description', function($annotation, $context = null) {
			list(, $description) = preg_split('~\s+|$~', $annotation, 2);
			if (null !== $context && $context instanceof ReflectionParameter) {
				$description = preg_replace('~^(\\$?' . $context->getName() . ')(\s+|$)~i', '\\2', $description, 1);
			}
			return $description;
		});
		$this->registerHelper('longDescription', function($element) {
			$short = $element->getAnnotation(ReflectionAnnotation::SHORT_DESCRIPTION);
			$long = $element->getAnnotation(ReflectionAnnotation::LONG_DESCRIPTION);

			if ($long) {
				$short .= "\n\n" . $long;
			}

			return $short;
		});
		$this->registerHelper('shortDescription', function($element) {
			return $element->getAnnotation(ReflectionAnnotation::SHORT_DESCRIPTION);
		});

		// Individual annotations processing
		$this->registerHelper('annotation', function($value, $name, $context) use ($that) {
			switch ($name) {
				case 'param':
				case 'return':
				case 'throws':
					$description = $that->description($value, $context);
					return '<code>' . $that->getTypeLinks($value, $context) . '</code>' . ($description ? '<br />' . $that->docline($description, $context) : '');
				case 'package':
					list($packageName, $description) = preg_split('~\s+|$~', $value, 2);
					return $that->packages
						? $that->link($that->getPackageUrl($packageName), $packageName) . ' ' . $that->docline($description, $context)
						: $that->docline($value, $context);
				case 'subpackage':
					if ($context->hasAnnotation('package')) {
						list($packageName) = preg_split('~\s+|$~', $context->annotations['package'][0], 2);
					} else {
						$packageName = '';
					}
					list($subpackageName, $description) = preg_split('~\s+|$~', $value, 2);

					return $that->packages && $packageName
						? $that->link($that->getPackageUrl($packageName . '\\' . $subpackageName), $subpackageName) . ' ' . $that->docline($description, $context)
						: $that->docline($value, $context);
				case 'see':
				case 'uses':
					$link = $that->resolveLink($value, $context);
					if (null !== $link) {
						return $link;
					}
					// Break missing intentionally
				default:
					return $that->docline($value, $context);
			}
		});

		$todo = $this->config->todo;
		$this->registerHelper('annotationFilter', function(array $annotations, array $filter = array()) use ($todo) {
			// Unsupported or deprecated annotations
			static $unsupported = array(
				ReflectionAnnotation::SHORT_DESCRIPTION, ReflectionAnnotation::LONG_DESCRIPTION,
				'property', 'property-read', 'property-write', 'method', 'abstract', 'access', 'final', 'filesource', 'global', 'name', 'static', 'staticvar'
			);
			foreach ($unsupported as $annotation) {
				unset($annotations[$annotation]);
			}

			// Custom filter
			foreach ($filter as $annotation) {
				unset($annotations[$annotation]);
			}

			// Show/hide todo
			if (!$todo) {
				unset($annotations['todo']);
			}

			return $annotations;
		});

		$this->registerHelper('annotationSort', function(array $annotations) {
			uksort($annotations, function($a, $b) {
				static $order = array(
					'deprecated' => 0, 'category' => 1, 'package' => 2, 'subpackage' => 3, 'copyright' => 4,
					'license' => 5, 'author' => 6, 'version' => 7, 'since' => 8, 'see' => 9, 'uses' => 10,
					'link' => 11, 'example' => 12, 'tutorial' => 13, 'todo' => 14
				);
				$orderA = isset($order[$a]) ? $order[$a] : 99;
				$orderB = isset($order[$b]) ? $order[$b] : 99;
				return $orderA - $orderB;
			});
			return $annotations;
		});

		// Static files versioning
		$destination = $this->config->destination;
		$this->registerHelper('staticFile', function($name) use ($destination) {
			static $versions = array();

			$filename = $destination . '/' . $name;
			if (!isset($versions[$filename]) && is_file($filename)) {
				$versions[$filename] = sprintf('%u', crc32(file_get_contents($filename)));
			}
			if (isset($versions[$filename])) {
				$name .= '?' . $versions[$filename];
			}
			return $name;
		});
	}

	/**
	 * Returns unified type value definition (class name or member data type).
	 *
	 * @param string $name
	 * @return string
	 */
	public function getTypeName($name)
	{
		static $names = array(
			'int' => 'integer',
			'bool' => 'boolean',
			'double' => 'float',
			'void' => '',
			'FALSE' => 'false',
			'TRUE' => 'true',
			'NULL' => 'null'
		);

		// Simple type
		if (isset($names[$name])) {
			return $names[$name];
		}

		// Class, constant or function
		return ltrim($name, '\\');
	}

	/**
	 * Returns links for types.
	 *
	 * @param string $annotation
	 * @param \Apigen\ReflectionBase|\TokenReflection\IReflection $context
	 * @return string
	 */
	public function getTypeLinks($annotation, $context)
	{
		$links = array();
		list($types) = preg_split('~\s+|$~', $annotation, 2);
		foreach (explode('|', $types) as $type) {
			$links[] = $this->resolveLink($type, $context) ?: $this->escapeHtml($this->getTypeName($type));
		}

		return implode('|', $links);
	}

	/**
	 * Returns links for namespace and its parent namespaces.
	 *
	 * @param string $namespace
	 * @param boolean $last
	 * @return string
	 */
	public function getNamespaceLinks($namespace, $last = true)
	{
		$links = array();

		$parent = '';
		foreach (explode('\\', $namespace) as $part) {
			$parent = ltrim($parent . '\\' . $part, '\\');
			$links[] = $last || $parent !== $namespace
				? $this->link($this->getNamespaceUrl($parent), $part)
				: $this->escapeHtml($part);
		}

		return implode('\\', $links);
	}

	/**
	 * Returns a link to a namespace summary file.
	 *
	 * @param string $namespaceName Namespace name
	 * @return string
	 */
	public function getNamespaceUrl($namespaceName)
	{
		return sprintf($this->config->templates['main']['namespace']['filename'], $this->urlize($namespaceName));
	}

	/**
	 * Returns a link to a package summary file.
	 *
	 * @param string $packageName Package name
	 * @return string
	 */
	public function getPackageUrl($packageName)
	{
		return sprintf($this->config->templates['main']['package']['filename'], $this->urlize($packageName));
	}

	/**
	 * Returns a link to class summary file.
	 *
	 * @param string|\Apigen\ReflectionClass $class Class reflection or name
	 * @return string
	 */
	public function getClassUrl($class)
	{
		$className = $class instanceof ReflectionClass ? $class->getName() : $class;
		return sprintf($this->config->templates['main']['class']['filename'], $this->urlize($className));
	}

	/**
	 * Returns a link to method in class summary file.
	 *
	 * @param \TokenReflection\IReflectionMethod $method Method reflection
	 * @return string
	 */
	public function getMethodUrl(ReflectionMethod $method)
	{
		return $this->getClassUrl($method->getDeclaringClassName()) . '#_' . $method->getName();
	}

	/**
	 * Returns a link to property in class summary file.
	 *
	 * @param \TokenReflection\IReflectionProperty $property Property reflection
	 * @return string
	 */
	public function getPropertyUrl(ReflectionProperty $property)
	{
		return $this->getClassUrl($property->getDeclaringClassName()) . '#$' . $property->getName();
	}

	/**
	 * Returns a link to constant in class summary file or to constant summary file.
	 *
	 * @param \Apigen\ReflectionConstant $constant Constant reflection
	 * @return string
	 */
	public function getConstantUrl(ReflectionConstant $constant)
	{
		// Class constant
		if ($className = $constant->getDeclaringClassName()) {
			return $this->getClassUrl($constant->getDeclaringClassName()) . '#' . $constant->getName();
		}
		// Constant in namespace or global space
		return sprintf($this->config->templates['main']['constant']['filename'], $this->urlize($constant->getName()));
	}

	/**
	 * Returns a link to function summary file.
	 *
	 * @param \Apigen\ReflectionFunction $method Function reflection
	 * @return string
	 */
	public function getFunctionUrl(ReflectionFunction $function)
	{
		return sprintf($this->config->templates['main']['function']['filename'], $this->urlize($function->getName()));
	}

	/**
	 * Returns a link to a element source code.
	 *
	 * @param \Apigen\ReflectionBase|\TokenReflection\IReflection $element Element reflection
	 * @param boolean $withLine Include file line number into the link
	 * @return string
	 */
	public function getSourceUrl($element, $withLine = true)
	{
		$file = '';

		if ($element instanceof ReflectionClass || $element instanceof ReflectionFunction || ($element instanceof ReflectionConstant && null === $element->getDeclaringClassName())) {
			$elementName = $element->getName();

			if ($element instanceof ReflectionFunction) {
				$file = 'function-';
			} elseif ($element instanceof ReflectionConstant) {
				$file = 'constant-';
			}
		} else {
			$elementName = $element->getDeclaringClassName();
		}

		$file .= $this->urlize($elementName);

		$line = null;
		if ($withLine) {
			$line = $element->getStartLine();
			if ($doc = $element->getDocComment()) {
				$line -= substr_count($doc, "\n") + 1;
			}
		}

		return sprintf($this->config->templates['main']['source']['filename'], $file) . (isset($line) ? "#$line" : '');
	}

	/**
	 * Returns a link to a element documentation at php.net.
	 *
	 * @param \Apigen\ReflectionBase|\TokenReflection\IReflectionMethod|\TokenReflection\IReflectionProperty $element Element reflection
	 * @return string
	 */
	public function getManualUrl($element)
	{
		static $manual = 'http://php.net/manual';
		static $reservedClasses = array('stdClass', 'Closure', 'Directory');

		// Extension
		if ($element instanceof ReflectionExtension) {
			$extensionName = strtolower($element->getName());
			if ('core' === $extensionName) {
				return $manual;
			}

			if ('date' === $extensionName) {
				$extensionName = 'datetime';
			}

			return sprintf('%s/book.%s.php', $manual, $extensionName);
		}

		// Class and its members
		$class = $element instanceof ReflectionClass ? $element : $element->getDeclaringClass();

		if (in_array($class->getName(), $reservedClasses)) {
			return $manual . '/reserved.classes.php';
		}

		$className = strtolower($class->getName());
		$classUrl = sprintf('%s/class.%s.php', $manual, $className);
		$elementName = strtolower(strtr(ltrim($element->getName(), '_'), '_', '-'));

		if ($element instanceof ReflectionClass) {
			return $classUrl;
		} elseif ($element instanceof ReflectionMethod) {
			return sprintf('%s/%s.%s.php', $manual, $className, $elementName);
		} elseif ($element instanceof ReflectionProperty) {
			return sprintf('%s#%s.props.%s', $classUrl, $className, $elementName);
		} elseif ($element instanceof ReflectionConstant) {
			return sprintf('%s#%s.constants.%s', $classUrl, $className, $elementName);
		}
	}

	/**
	 * Tries to resolve string as class, interface or exception name.
	 *
	 * @param string $className Class name description
	 * @param string $namespace Namespace name
	 * @return \Apigen\ReflectionClass
	 */
	public function getClass($className, $namespace = null)
	{
		if ('\\' === substr($className, 0, 1)) {
			$namespace = '';
			$className = substr($className, 1);
		}

		if (isset($this->classes[$namespace . '\\' . $className])) {
			$name = $namespace . '\\' . $className;
		} elseif (isset($this->classes[$className])) {
			$name = $className;
		} else {
			return null;
		}

		// Class is not "documented"
		if (!$this->classes[$name]->isDocumented()) {
			return null;
		}

		return $this->classes[$name];
	}

	/**
	 * Tries to resolve type as constant name.
	 *
	 * @param string $constantName Constant name
	 * @param string $namespace Namespace name
	 * @return \Apigen\ReflectionConstant
	 */
	public function getConstant($constantName, $namespace = null)
	{
		if ('\\' === substr($constantName, 0, 1)) {
			$namespace = '';
			$constantName = substr($constantName, 1);
		}

		if (isset($this->constants[$namespace . '\\' . $constantName])) {
			return $this->constants[$namespace . '\\' . $constantName];
		}

		if (isset($this->constants[$constantName])) {
			return $this->constants[$constantName];
		}

		return null;
	}

	/**
	 * Tries to resolve type as function name.
	 *
	 * @param string $functionName Function name
	 * @param string $namespace Namespace name
	 * @return \Apigen\ReflectionFunction
	 */
	public function getFunction($functionName, $namespace = null)
	{
		if ('\\' === substr($functionName, 0, 1)) {
			$namespace = '';
			$functionName = substr($functionName, 1);
		}

		if (isset($this->functions[$namespace . '\\' . $functionName])) {
			return $this->functions[$namespace . '\\' . $functionName];
		}

		if (isset($this->functions[$functionName])) {
			return $this->functions[$functionName];
		}

		return null;
	}

	/**
	 * Tries to parse a definition of a class/method/property/constant/function and returns the appropriate instance if successful.
	 *
	 * @param string $definition Definition
	 * @param \Apigen\ReflectionBase|\TokenReflection\IReflection $context Link context
	 * @return \Apigen\ReflectionBase|\TokenReflection\IReflection|null
	 */
	public function resolveElement($definition, $context)
	{
		if (empty($definition)) {
			return null;
		}

		if ($context instanceof ReflectionParameter && null === $context->getDeclaringClassName()) {
			$context = $this->functions[$context->getDeclaringFunctionName()];
		} elseif (!$context instanceof ReflectionClass && !$context instanceof ReflectionConstant && !$context instanceof ReflectionFunction) {
			$context = $this->classes[$context->getDeclaringClassName()];
		}

		if (($class = $this->getClass(\TokenReflection\ReflectionBase::resolveClassFQN($definition, $context->getNamespaceAliases(), $context->getNamespaceName()), $context->getNamespaceName()))
			|| ($class = $this->getClass($definition, $context->getNamespaceName()))) {
			// Class

			// No "documented" class
			if (!$class->isDocumented()) {
				return null;
			}

			return $class;
		} elseif ($constant = $this->getConstant($definition, $context->getNamespaceName())) {
			// Constant
			return $constant;
		} elseif ($function = $this->getFunction($definition, $context->getNamespaceName())) {
			// Function
			return $function;
		}

		// Class::something or Class->something
		if (($pos = strpos($definition, '::')) || ($pos = strpos($definition, '->'))) {
			$class = $this->getClass(substr($definition, 0, $pos), $context->getNamespaceName());

			if (null === $class) {
				$class = $this->getClass(\TokenReflection\ReflectionBase::resolveClassFQN(substr($definition, 0, $pos), $context->getNamespaceAliases(), $context->getNamespaceName()));
			}

			// No class
			if (null === $class) {
				return null;
			}

			$context = $class;

			$definition = substr($definition, $pos + 2);
		}

		// No "documented" context
		if (($context instanceof ReflectionClass || $context instanceof ReflectionConstant || $context instanceof ReflectionFunction)
				&& !$context->isDocumented()) {
			return null;
		}

		if ($context->hasProperty($definition)) {
			// Class property
			return $context->getProperty($definition);
		} elseif ('$' === $definition{0} && $context->hasProperty(substr($definition, 1))) {
			// Class $property
			return $context->getProperty(substr($definition, 1));
		} elseif ($context->hasMethod($definition)) {
			// Class method
			return $context->getMethod($definition);
		} elseif ('()' === substr($definition, -2) && $context->hasMethod(substr($definition, 0, -2))) {
			// Class method()
			return $context->getMethod(substr($definition, 0, -2));
		} elseif ($context->hasConstant($definition)) {
			// Class constant
			return $context->getConstantReflection($definition);
		}

		return null;
	}

	/**
	 * Tries to parse a definition of a class/method/property/constant/function and returns the appropriate link if successful.
	 *
	 * @param string $definition Definition
	 * @param \Apigen\ReflectionBase|\TokenReflection\IReflection $context Link context
	 * @return string|null
	 */
	public function resolveLink($definition, $context)
	{
		if (empty($definition)) {
			return null;
		}

		$element = $this->resolveElement($definition, $context);
		if (null === $element) {
			return null;
		}

		if ($element instanceof ReflectionClass) {
			return $this->link($this->getClassUrl($element), $element->getName());
		} elseif ($element instanceof ReflectionConstant && null === $element->getDeclaringClassName()) {
			return $this->link($this->getConstantUrl($element), $element->getName());
		} elseif ($element instanceof ReflectionFunction) {
			return $this->link($this->getFunctionUrl($element), $element->getName());
		}

		$text = $element->getDeclaringClassName();
		if ($element instanceof ReflectionProperty) {
			$url = $this->propertyUrl($element);
			$text .= '::$' . $element->getName();
		} elseif ($element instanceof ReflectionMethod) {
			$url = $this->methodUrl($element);
			$text .= '::' . $element->getName() . '()';
		} elseif ($element instanceof ReflectionConstant) {
			$url = $this->constantUrl($element);
			$text .= '::' . $element->getName();
		}

		return $this->link($url, $text);
	}

	/**
	 * Resolves links in documentation.
	 *
	 * @param string $text Processed documentation text
	 * @param \Apigen\ReflectionBase|\TokenReflection\IReflection $context Reflection object
	 * @return string
	 */
	public function resolveLinks($text, $context)
	{
		$that = $this;
		return preg_replace_callback('~{@link\\s+([^}]+)}~', function ($matches) use ($context, $that) {
			return $that->resolveLink($matches[1], $context) ?: $matches[0];
		}, $text);
	}

	/**
	 * Returns link.
	 *
	 * @param string $url
	 * @param string $text
	 * @return string
	 */
	public function link($url, $text)
	{
		return sprintf('<a href="%s">%s</a>', $url, $this->escapeHtml($text));
	}

	/**
	 * Converts string to url safe characters.
	 *
	 * @param string $string
	 * @return string
	 */
	private function urlize($string)
	{
		return preg_replace('~[^\w]~', '.', $string);
	}
}
