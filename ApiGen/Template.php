<?php

/**
 * ApiGen 2.0 - API documentation generator.
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011 Ondřej Nešpor (http://andrewsville.cz)
 * Copyright (c) 2011 Jaroslav Hanslík (http://kukulich.cz)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen;

use Nette, FSHL;
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
	 * @var \ApiGen\Config
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
	 * Texy.
	 *
	 * @var Texy
	 */
	private $texy;

	/**
	 * Creates template.
	 *
	 * @param \ApiGen\Generator $generator
	 */
	public function __construct(Generator $generator)
	{
		$this->config = $generator->getConfig();
		$this->classes = $generator->getClasses();
		$this->constants = $generator->getConstants();
		$this->functions = $generator->getFunctions();

		$that = $this;

		// FSHL
		$fshl = new FSHL\Highlighter(new FSHL\Output\Html());
		$fshlPhpLexer = new FSHL\Lexer\Php();

		// Texy
		$this->texy = new \Texy();
		$this->texy->allowedTags = array_flip($this->config->allowedHtml);
		$this->texy->allowed['list/definition'] = false;
		$this->texy->allowed['phrase/em-alt'] = false;
		$this->texy->allowed['longwords'] = false;
		$this->texy->allowed['typography'] = false;
		$this->texy->linkModule->shorten = false;
		// Highlighting <code>, <pre>
		$this->texy->registerBlockPattern(
			function($parser, $matches, $name) use ($fshl, $fshlPhpLexer) {
				$content = 'code' === $matches[1] ? $fshl->highlight($fshlPhpLexer, $matches[2]) : htmlspecialchars($matches[2]);
				$content = $parser->getTexy()->protect($content, \Texy::CONTENT_BLOCK);
				return \TexyHtml::el('pre', $content);
			},
			'~<(code|pre)>(.+?)</\1>~s',
			'codeBlockSyntax'
		);

		$latte = new Nette\Latte\Engine();
		$macroSet = new Nette\Latte\Macros\MacroSet($latte->parser);
		$macroSet->addMacro('try', 'try {', '} catch (\Exception $e) {}');
		$this->registerFilter($latte);

		// Common operations
		$this->registerHelperLoader('Nette\Templating\DefaultHelpers::loader');

		// PHP source highlight
		$this->registerHelper('highlightPHP', function($source, $context) use ($that, $fshl, $fshlPhpLexer) {
			return $that->resolveLink($source, $context) ?: $fshl->highlight($fshlPhpLexer, (string) $source);
		});
		$this->registerHelper('highlightValue', function($definition, $context) use ($that) {
			return $that->highlightPHP(preg_replace('~^(?:[ ]{4}|\t)~m', '', $definition), $context);
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

		// Types
		$this->registerHelper('typeLinks', new Nette\Callback($this, 'getTypeLinks'));
		$this->registerHelper('type', function($value) use ($that) {
			$type = $that->getTypeName(gettype($value));
			return 'null' !== $type ? $type : '';
		});

		// Docblock descriptions
		$this->registerHelper('description', function($annotation, $context) use ($that) {
			list(, $description) = $that->split($annotation);
			if ($context instanceof ReflectionParameter) {
				$description = preg_replace('~^(\\$?' . $context->getName() . ')(\s+|$)~i', '\\2', $description, 1);
			}
			return $that->doc($description, $context);
		});
		$this->registerHelper('shortDescription', function($element) use ($that) {
			return $that->doc($element->getAnnotation(ReflectionAnnotation::SHORT_DESCRIPTION), $element);
		});
		$this->registerHelper('longDescription', function($element) use ($that) {
			$short = $element->getAnnotation(ReflectionAnnotation::SHORT_DESCRIPTION);
			$long = $element->getAnnotation(ReflectionAnnotation::LONG_DESCRIPTION);

			if ($long) {
				$short .= "\n\n" . $long;
			}

			// Merge lines
			$short = preg_replace_callback('~(?:<(code|pre)>.+?</\1>)|([^<]*)~s', function($matches) {
				return !empty($matches[2])
					? preg_replace('~\n(?:\t|[ ])+~', ' ', $matches[2])
					: $matches[0];
			}, $short);

			return $that->doc($short, $element, true);
		});

		// Individual annotations processing
		$this->registerHelper('annotation', function($value, $name, $context) use ($that) {
			switch ($name) {
				case 'param':
				case 'return':
				case 'throws':
					$description = $that->description($value, $context);
					return sprintf('<code>%s</code>%s', $that->getTypeLinks($value, $context), $description ? '<br />' . $description : '');
				case 'package':
					list($packageName, $description) = $that->split($value);
					if ($that->packages) {
						return $that->link($that->getPackageUrl($packageName), $packageName) . ' ' . $that->doc($description, $context);
					}
					break;
				case 'subpackage':
					if ($context->hasAnnotation('package')) {
						list($packageName) = $that->split($context->annotations['package'][0]);
					} else {
						$packageName = '';
					}
					list($subpackageName, $description) = $that->split($value);

					if ($that->packages && $packageName) {
						return $that->link($that->getPackageUrl($packageName . '\\' . $subpackageName), $subpackageName) . ' ' . $that->doc($description, $context);
					}
					break;
				case 'see':
				case 'uses':
					list($link, $description) = $that->split($value);
					$separator = $context instanceof ReflectionClass || !$description ? ' ' : '<br />';
					if (null !== $that->resolveElement($link, $context)) {
						return sprintf('<code>%s</code>%s%s', $that->getTypeLinks($link, $context), $separator, $description);
					}
					break;
				default:
					break;
			}

			// Default
			return $that->doc($value, $context);
		});

		$todo = $this->config->todo;
		$internal = $this->config->internal;
		$this->registerHelper('annotationFilter', function(array $annotations, array $filter = array()) use ($todo, $internal) {
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

			// Show/hide internal
			if (!$internal) {
				unset($annotations['internal']);
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
					'link' => 11, 'internal' => 14, 'example' => 13, 'tutorial' => 14, 'todo' => 15
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
	 * @param \ApiGen\ReflectionBase|\TokenReflection\IReflection $context
	 * @return string
	 */
	public function getTypeLinks($annotation, $context)
	{
		$links = array();
		list($types) = $this->split($annotation);
		foreach (explode('|', $types) as $type) {
			$type = $this->getTypeName($type);
			$links[] = $this->resolveLink($type, $context) ?: $this->escapeHtml($type);
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
	 * @param string|\ApiGen\ReflectionClass $class Class reflection or name
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
	 * @param \ApiGen\ReflectionConstant $constant Constant reflection
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
	 * @param \ApiGen\ReflectionFunction $function Function reflection
	 * @return string
	 */
	public function getFunctionUrl(ReflectionFunction $function)
	{
		return sprintf($this->config->templates['main']['function']['filename'], $this->urlize($function->getName()));
	}

	/**
	 * Returns a link to a element source code.
	 *
	 * @param \ApiGen\ReflectionBase|\TokenReflection\IReflection $element Element reflection
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

		return sprintf($this->config->templates['main']['source']['filename'], $file) . (isset($line) ? '#' . $line : '');
	}

	/**
	 * Returns a link to a element documentation at php.net.
	 *
	 * @param \ApiGen\ReflectionBase|\TokenReflection\IReflection $element Element reflection
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
	 * @return \ApiGen\ReflectionClass
	 */
	public function getClass($className, $namespace = '')
	{
		if (isset($this->classes[$namespace . '\\' . $className])) {
			$class = $this->classes[$namespace . '\\' . $className];
		} elseif (isset($this->classes[$className])) {
			$class = $this->classes[$className];
		} else {
			return null;
		}

		// Class is not "documented"
		if (!$class->isDocumented()) {
			return null;
		}

		return $class;
	}

	/**
	 * Tries to resolve type as constant name.
	 *
	 * @param string $constantName Constant name
	 * @param string $namespace Namespace name
	 * @return \ApiGen\ReflectionConstant
	 */
	public function getConstant($constantName, $namespace = '')
	{
		if (isset($this->constants[$namespace . '\\' . $constantName])) {
			$constant = $this->constants[$namespace . '\\' . $constantName];
		} elseif (isset($this->constants[$constantName])) {
			$constant = $this->constants[$constantName];
		} else {
			return null;
		}

		// Constant is not "documented"
		if (!$constant->isDocumented()) {
			return null;
		}

		return $constant;
	}

	/**
	 * Tries to resolve type as function name.
	 *
	 * @param string $functionName Function name
	 * @param string $namespace Namespace name
	 * @return \ApiGen\ReflectionFunction
	 */
	public function getFunction($functionName, $namespace = '')
	{
		if (isset($this->functions[$namespace . '\\' . $functionName])) {
			$function = $this->functions[$namespace . '\\' . $functionName];
		} elseif (isset($this->functions[$functionName])) {
			$function = $this->functions[$functionName];
		} else {
			return null;
		}

		// Function is not "documented"
		if (!$function->isDocumented()) {
			return null;
		}

		return $function;
	}

	/**
	 * Tries to parse a definition of a class/method/property/constant/function and returns the appropriate instance if successful.
	 *
	 * @param string $definition Definition
	 * @param \ApiGen\ReflectionBase|\TokenReflection\IReflection $context Link context
	 * @return \ApiGen\ReflectionBase|\TokenReflection\IReflection|null
	 */
	public function resolveElement($definition, $context)
	{
		// No simple type resolving
		static $types = array(
			'boolean' => 1, 'integer' => 1, 'float' => 1, 'string' => 1,
			'array' => 1, 'object' => 1, 'resource' => 1, 'callback' => 1,
			'null' => 1, 'false' => 1, 'true' => 1
		);

		if (empty($definition) || isset($types[$definition])) {
			return null;
		}

		if ($context instanceof ReflectionParameter && null === $context->getDeclaringClassName()) {
			// Parameter of function in namespace or global space
			$context = $this->getFunction($context->getDeclaringFunctionName());
		} elseif ($context instanceof ReflectionMethod || $context instanceof ReflectionParameter || ($context instanceof ReflectionConstant && null !== $context->getDeclaringClassName()) || $context instanceof ReflectionProperty) {
			// Member of a class
			$context = $this->getClass($context->getDeclaringClassName());
		}

		if (($class = $this->getClass(\TokenReflection\Resolver::resolveClassFQN($definition, $context->getNamespaceAliases(), $context->getNamespaceName()), $context->getNamespaceName()))
			|| ($class = $this->getClass($definition, $context->getNamespaceName()))) {
			// Class
			return $class;
		} elseif ($constant = $this->getConstant($definition, $context->getNamespaceName())) {
			// Constant
			return $constant;
		} elseif (($function = $this->getFunction($definition, $context->getNamespaceName()))
			|| ('()' === substr($definition, -2) && ($function = $this->getFunction(substr($definition, 0, -2), $context->getNamespaceName())))) {
			// Function
			return $function;
		} elseif (($pos = strpos($definition, '::')) || ($pos = strpos($definition, '->'))) {
			// Class::something or Class->something
			if (0 === strpos($definition, 'parent::') && ($parentClassName = $context->getParentClassName())) {
				$context = $this->getClass($parentClassName);
			} elseif (0 !== strpos($definition, 'self::')) {
				$class = $this->getClass(substr($definition, 0, $pos), $context->getNamespaceName());

				if (null === $class) {
					$class = $this->getClass(\TokenReflection\Resolver::resolveClassFQN(substr($definition, 0, $pos), $context->getNamespaceAliases(), $context->getNamespaceName()));
				}

				$context = $class;
			}

			$definition = substr($definition, $pos + 2);
		}

		// No usable context
		if (null === $context || $context instanceof ReflectionConstant || $context instanceof ReflectionFunction) {
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
	 * @param \ApiGen\ReflectionBase|\TokenReflection\IReflection $context Link context
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
			$link = $this->link($this->getClassUrl($element), $element->getName());
		} elseif ($element instanceof ReflectionConstant && null === $element->getDeclaringClassName()) {
			$text = $element->inNamespace()
				? $this->escapeHtml($element->getNamespaceName()) . '\\<b>' . $this->escapeHtml($element->getShortName()) . '</b>'
				: '<b>' . $this->escapeHtml($element->getName()) . '</b>';
			$link = $this->link($this->getConstantUrl($element), $text, false);
		} elseif ($element instanceof ReflectionFunction) {
			$link = $this->link($this->getFunctionUrl($element), $element->getName() . '()');
		} else {
			$text = $this->escapeHtml($element->getDeclaringClassName());
			if ($element instanceof ReflectionProperty) {
				$url = $this->propertyUrl($element);
				$text .= '::<var>$' . $this->escapeHtml($element->getName()) . '</var>';
			} elseif ($element instanceof ReflectionMethod) {
				$url = $this->methodUrl($element);
				$text .= '::' . $this->escapeHtml($element->getName()) . '()';
			} elseif ($element instanceof ReflectionConstant) {
				$url = $this->constantUrl($element);
				$text .= '::<b>' . $this->escapeHtml($element->getName()) . '</b>';
			}

			$link = $this->link($url, $text, false);
		}

		return sprintf('<code>%s</code>', $link);
	}

	/**
	 * Resolves links in documentation.
	 *
	 * @param string $text Processed documentation text
	 * @param \ApiGen\ReflectionBase|\TokenReflection\IReflection $context Reflection object
	 * @return string
	 */
	private function resolveLinks($text, $context)
	{
		$that = $this;
		return preg_replace_callback('~{@(?:link|see)\\s+([^}]+)}~', function ($matches) use ($context, $that) {
			return $that->resolveLink($matches[1], $context) ?: $matches[1];
		}, $text);
	}

	/**
	 * Resolves internal annotation.
	 *
	 * @param string $text
	 * @return string
	 */
	private function resolveInternal($text)
	{
		$internal = $this->config->internal;
		return preg_replace_callback('~\\{@(\\w+)(?:(?:\\s+((?>(?R)|[^{}]+)*)\\})|\\})~', function($matches) use ($internal) {
			// Replace only internal
			if ('internal' !== $matches[1]) {
				return $matches[0];
			}
			return $internal && isset($matches[2]) ? $matches[2] : '';
		}, $text);
	}

	/**
	 * Formats text as documentation block or line.
	 *
	 * @param string $text Text
	 * @param \ApiGen\ReflectionBase|\TokenReflection\IReflection $context Reflection object
	 * @param boolean $block Parse text as block
	 * @return string
	 */
	public function doc($text, $context, $block = false)
	{
		return $this->resolveLinks($this->texy->process($this->resolveInternal($text), !$block), $context);
	}

	/**
	 * Parses annotation value.
	 *
	 * @param string $value
	 * @return array
	 */
	public function split($value)
	{
		return preg_split('~\s+|$~', $value, 2);
	}

	/**
	 * Returns link.
	 *
	 * @param string $url
	 * @param string $text
	 * @param boolean $escape If the text should be escaped
	 * @return string
	 */
	public function link($url, $text, $escape = true)
	{
		return sprintf('<a href="%s">%s</a>', $url, $escape ? $this->escapeHtml($text) : $text);
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
