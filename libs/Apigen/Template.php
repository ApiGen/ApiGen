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

		$latte = new Nette\Latte\Engine;
		$latte->parser->macros['try'] = '<?php try { ?>';
		$latte->parser->macros['/try'] = '<?php } catch (\Exception $e) {} ?>';
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
			return $that->highlightPHP(preg_replace('#^(?:[ ]{4}|\t)#m', '', $definition));
		});

		// Url
		$this->registerHelper('packageUrl', new Nette\Callback($this, 'getPackageUrl'));
		$this->registerHelper('namespaceUrl', new Nette\Callback($this, 'getNamespaceUrl'));
		$this->registerHelper('classUrl', new Nette\Callback($this, 'getClassUrl'));
		$this->registerHelper('methodUrl', new Nette\Callback($this, 'getMethodUrl'));
		$this->registerHelper('propertyUrl', new Nette\Callback($this, 'getPropertyUrl'));
		$this->registerHelper('constantUrl', new Nette\Callback($this, 'getConstantUrl'));
		$this->registerHelper('functionUrl', new Nette\Callback($this, 'getFunctionUrl'));
		$this->registerHelper('sourceUrl', new Nette\Callback($this, 'getSourceUrl'));
		$this->registerHelper('manualUrl', new Nette\Callback($this, 'getManualUrl'));

		$this->registerHelper('namespaceLinks', new Nette\Callback($this, 'getNamespaceLinks'));

		// Types
		$this->registerHelper('getTypes', new Nette\Callback($this, 'getTypes'));
		$this->registerHelper('resolveType', function($variable) {
			return is_object($variable) ? get_class($variable) : gettype($variable);
		});
		$this->registerHelper('resolveClass', new Nette\Callback($this, 'resolveClass'));

		// Texy
		$texy = new \Texy;
		$linkModule = new \TexyLinkModule($texy);
		$linkModule->shorten = FALSE;
		$texy->linkModule = $linkModule;
		$texy->mergeLines = FALSE;
		$texy->allowedTags = array_flip($this->config->allowedHtml);
		$texy->allowed['list/definition'] = FALSE;
		$texy->allowed['phrase/em-alt'] = FALSE;
		$texy->allowed['longwords'] = FALSE;
		// Highlighting <code>, <pre>
		$texy->registerBlockPattern(
			function($parser, $matches, $name) use ($fshl) {
				$content = $matches[1] === 'code' ? $fshl->highlightString('PHP', $matches[2]) : htmlSpecialChars($matches[2]);
				$content = $parser->getTexy()->protect($content, \Texy::CONTENT_BLOCK);
				return \TexyHtml::el('pre', $content);
			},
			'#<(code|pre)>(.+?)</\1>#s',
			'codeBlockSyntax'
		);

		// Documentation formatting
		$this->registerHelper('resolveLinks', new Nette\Callback($this, 'resolveLinks'));
		$this->registerHelper('docline', function($text) use ($texy) {
			return $texy->processLine($text);
		});
		$this->registerHelper('docblock', function($text) use ($texy) {
			return $texy->process($text);
		});
		$this->registerHelper('doclabel', function($doc, $namespace, ReflectionParameter $parameter = null) use ($that) {
			@list($names, $label) = preg_split('#\s+#', $doc, 2);
			$res = '';
			foreach (explode('|', $names) as $name) {
				if (null !== $parameter && $name === $parameter->getOriginalClassName()) {
					$name = $parameter->getClassName();
				}

				$class = $that->resolveClass($name, $namespace);
				$res .= $class !== null ? sprintf('<a href="%s">%s</a>', $that->classUrl($class), $that->escapeHtml($class)) : $that->escapeHtml($that->resolveName($name));
				$res .= '|';
			}

			if (null !== $parameter) {
				$label = preg_replace('~^(\\$?' . $parameter->getName() . ')(\s+|$)~i', '\\2', $label, 1);
			}

			return rtrim($res, '|') . (!empty($label) ? '<br />' . $that->escapeHtml($label) : '');
		});
		$this->registerHelper('docdescription', function($doc, $no) {
			$parts = preg_split('#\s+#', $doc, $no);
			return isset($parts[$no - 1]) ? $parts[$no - 1] : '';
		});

		// Docblock descriptions
		$this->registerHelper('longDescription', function($element, $shortIfNone = false) {
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
		$this->registerHelper('annotation', function($value, $name, $parent) use ($that) {
			switch ($name) {
				case 'package':
					@list($packageName, $description) = preg_split('~\s+~', $value, 2);
					return $that->packages
						? '<a href="' . $that->getPackageUrl($packageName) . '">' . $that->escapeHtml($packageName) . '</a> ' . $that->escapeHtml($description)
						: $that->escapeHtml($value);
				case 'subpackage':
					if ($parent->hasAnnotation('package')) {
						list($packageName) = preg_split('~\s+~', $parent->annotations['package'][0], 2);
					} else {
						$packageName = '';
					}
					@list($subpackageName, $description) = preg_split('~\s+~', $value, 2);

					return $that->packages && $packageName
						? '<a href="' . $that->getPackageUrl($packageName . '\\' . $subpackageName) . '">' . $that->escapeHtml($subpackageName) . '</a> ' . $that->escapeHtml($description)
						: $that->escapeHtml($value);
				case 'see':
				case 'uses':
					$link = $that->resolveClassLink($value, $parent);
					if (null !== $link) {
						return $link;
					}
					// Break missing intentionally
				default:
					return $that->resolveLinks($that->docline($value), $parent);
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
		$this->registerHelper('staticFile', function($name, $line = null) use ($destination) {
			static $versions = array();

			$filename = $destination . '/' . $name;
			if (!isset($versions[$filename]) && file_exists($filename)) {
				$versions[$filename] = sprintf('%u', crc32(file_get_contents($filename)));
			}
			if (isset($versions[$filename])) {
				$name .= '?' . $versions[$filename];
			}
			return $name;
		});

		// Namespaces
		$this->registerHelper('subnamespaceName', function($namespaceName) {
			if ($pos = strrpos($namespaceName, '\\')) {
				return substr($namespaceName, $pos + 1);
			}
			return $namespaceName;
		});

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
				? '<a href="' . $this->getNamespaceUrl($parent) . '">' . $this->escapeHtml($part) . '</a>'
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
		return sprintf($this->config->templates['main']['namespace']['filename'], preg_replace('#[^a-z0-9_]#i', '.', $namespaceName));
	}

	/**
	 * Returns a link to a package summary file.
	 *
	 * @param string $packageName Package name
	 * @return string
	 */
	public function getPackageUrl($packageName)
	{
		return sprintf($this->config->templates['main']['package']['filename'], preg_replace('#[^a-z0-9_]#i', '.', $packageName));
	}

	/**
	 * Returns a link to class summary file.
	 *
	 * @param string|\Apigen\ReflectionClass $class Class reflection or name
	 * @return string
	 */
	public function getClassUrl($class)
	{
		if ($class instanceof ReflectionClass) {
			$class = $class->getName();
		}

		return sprintf($this->config->templates['main']['class']['filename'], preg_replace('#[^a-z0-9_]#i', '.', $class));
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
		return sprintf($this->config->templates['main']['constant']['filename'], preg_replace('#[^a-z0-9_]#i', '.', $constant->getName()));
	}

	/**
	 * Returns a link to function summary file.
	 *
	 * @param \Apigen\ReflectionFunction $method Function reflection
	 * @return string
	 */
	public function getFunctionUrl(ReflectionFunction $function)
	{
		return sprintf($this->config->templates['main']['function']['filename'], preg_replace('#[^a-z0-9_]#i', '.', $function->getName()));
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

		$file .= preg_replace('#[^a-z0-9_]#i', '.', str_replace('\\', '/', $elementName));

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
	 * Returns a list of resolved types from @param or @return tags.
	 *
	 * @param \Apigen\ReflectionBase|\TokenReflection\ReflectionMethod|\TokenReflection\ReflectionProperty $element Element reflection
	 * @param integer $position Parameter position
	 * @return array
	 */
	public function getTypes($element, $position = NULL, $annotationName = '')
	{
		$annotation = array();
		if ($element instanceof ReflectionProperty || $element instanceof ReflectionConstant) {
			$annotation = $element->getAnnotation('var');
			if (null === $annotation && !$element->isTokenized()) {
				$value = $element->getDefaultValue();
				if (null !== $value) {
					$annotation = gettype($value);
				}
			}
		} elseif ($element instanceof ReflectionFunction && !empty($annotationName)) {
			$annotation = $element->getAnnotation($annotationName);
		} elseif ($element instanceof ReflectionMethod || $element instanceof ReflectionFunction) {
			$annotation = $position === null ? $element->getAnnotation('return') : @$element->annotations['param'][$position];
		}

		if ($element instanceof ReflectionFunction) {
			$namespace = $element->getNamespaceName();
		} elseif ($element instanceof ReflectionParameter) {
			$namespace = $element->getDeclaringFunction()->getNamespaceName();
		} else {
			$namespace = $element->getDeclaringClass()->getNamespaceName();
		}

		$types = array();
		foreach (preg_replace('#\s.*#', '', (array) $annotation) as $s) {
			foreach (explode('|', $s) as $name) {
				$class = $this->resolveClass($name, $namespace);
				$types[] = (object) array('name' => $class ?: $this->resolveName($name), 'class' => $class);
			}
		}
		return $types;
	}

	/**
	 * Resolves a parameter value definition (class name or parameter data type).
	 *
	 * @param string $name Parameter definition
	 * @return string
	 */
	public function resolveName($name)
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

		$name = ltrim($name, '\\');
		if (isset($names[$name])) {
			return $names[$name];
		}
		return $name;
	}

	/**
	 * Tries to resolve type as class or interface name.
	 *
	 * @param string $className Class name description
	 * @param string $namespace Namespace name
	 * @return string
	 */
	public function resolveClass($className, $namespace = NULL)
	{
		if (substr($className, 0, 1) === '\\') {
			$namespace = '';
			$className = substr($className, 1);
		}

		$name = isset($this->classes["$namespace\\$className"]) ? "$namespace\\$className" : (isset($this->classes[$className]) ? $className : null);
		if (null !== $name && !$this->classes[$name]->isDocumented()) {
			$name = null;
		}
		return $name;
	}

	/**
	 * Tries to resolve type as function name.
	 *
	 * @param string $functionName Function name
	 * @param string $namespace Namespace name
	 * @return string
	 */
	public function resolveFunction($functionName, $namespace = NULL)
	{
		if (substr($functionName, 0, 1) === '\\') {
			$namespace = '';
			$functionName = substr($functionName, 1);
		}

		if (isset($this->functions[$namespace . '\\' . $functionName])) {
			return $namespace . '\\' . $functionName;
		}

		if (isset($this->functions[$functionName])) {
			return $functionName;
		}

		return null;
	}

	/**
	 * Tries to resolve type as constant name.
	 *
	 * @param string $constantName Constant name
	 * @param string $namespace Namespace name
	 * @return string
	 */
	public function resolveConstant($constantName, $namespace = NULL)
	{
		if (substr($constantName, 0, 1) === '\\') {
			$namespace = '';
			$constantName = substr($constantName, 1);
		}

		if (isset($this->constants[$namespace . '\\' . $constantName])) {
			return $namespace . '\\' . $constantName;
		}

		if (isset($this->constants[$constantName])) {
			return $constantName;
		}

		return null;
	}

	/**
	 * Resolves links in documentation.
	 *
	 * @param string $text Processed documentation text
	 * @param \Apigen\ReflectionBase|\TokenReflection\IReflection $element Reflection object
	 * @return string
	 */
	public function resolveLinks($text, $element)
	{
		$that = $this;
		return preg_replace_callback('~{@link\\s+([^}]+)}~', function ($matches) use ($element, $that) {
			return $that->resolveClassLink($matches[1], $element) ?: $matches[0];
		}, $text);
	}

	/**
	 * Tries to parse a link to a class/method/property and returns the appropriate link if successful.
	 *
	 * @param string $link Link definition
	 * @param \Apigen\ReflectionBase|\TokenReflection\IReflection $context Link context
	 * @return string|null
	 */
	public function resolveClassLink($link, $context)
	{
		if (!$context instanceof ReflectionClass && !$context instanceof ReflectionConstant && !$context instanceof ReflectionFunction) {
			$context = $this->classes[$context->getDeclaringClassName()];
		}

		if (($pos = strpos($link, '::')) || ($pos = strpos($link, '->'))) {
			// Class::something or Class->something
			$className = $this->resolveClass(substr($link, 0, $pos), $context->getNamespaceName());

			if (null === $className) {
				$className = $this->resolveClass(\TokenReflection\ReflectionBase::resolveClassFQN(substr($link, 0, $pos), $context->getNamespaceAliases(), $context->getNamespaceName()));
			}

			// No class
			if (null === $className) {
				return null;
			}

			$context = $this->classes[$className];

			$link = substr($link, $pos + 2);
		} elseif ((null !== ($className = $this->resolveClass(\TokenReflection\ReflectionBase::resolveClassFQN($link, $context->getNamespaceAliases(), $context->getNamespaceName()), $context->getNamespaceName())))
			|| null !== ($className = $this->resolveClass($link, $context->getNamespaceName()))) {
			// Class
			$context = $this->classes[$className];

			// No "documented" class
			if (!$context->isDocumented()) {
				return null;
			}

			return '<a href="' . $this->classUrl($context) . '">' . $this->escapeHtml($className) . '</a>';
		} elseif ($functionName = $this->resolveFunction($link, $context->getNamespaceName())) {
			// Function
			$context = $this->functions[$functionName];

			return '<a href="' . $this->functionUrl($context) . '">' . $this->escapeHtml($functionName) . '</a>';
		} elseif ($constantName = $this->resolveConstant($link, $context->getNamespaceName())) {
			// Constant
			$context = $this->constants[$constantName];

			return '<a href="' . $this->constantUrl($context) . '">' . $this->escapeHtml($constantName) . '</a>';
		}

		// No "documented" context
		if (($context instanceof ReflectionClass || $context instanceof ReflectionConstant || $context instanceof ReflectionFunction)
				&& !$context->isDocumented()) {
			return null;
		}

		if ($context->hasProperty($link)) {
			// Class property
			$reflection = $context->getProperty($link);
		} elseif ('$' === $link{0} && $context->hasProperty(substr($link, 1))) {
			// Class $property
			$reflection = $context->getProperty(substr($link, 1));
		} elseif ($context->hasMethod($link)) {
			// Class method
			$reflection = $context->getMethod($link);
		} elseif ('()' === substr($link, -2) && $context->hasMethod(substr($link, 0, -2))) {
			// Class method()
			$reflection = $context->getMethod(substr($link, 0, -2));
		} elseif ($context->hasConstant($link)) {
			// Class constant
			$reflection = $context->getConstantReflection($link);
		} else {
			return null;
		}

		$value = $reflection->getDeclaringClassName();
		if ($reflection instanceof ReflectionProperty) {
			$link = $this->propertyUrl($reflection);
			$value .= '::$' . $reflection->getName();
		} elseif ($reflection instanceof ReflectionMethod) {
			$link = $this->methodUrl($reflection);
			$value .= '::' . $reflection->getName() . '()';
		} elseif ($reflection instanceof ReflectionConstant) {
			$link = $this->constantUrl($reflection);
			$value .= '::' . $reflection->getName();
		}

		return '<a href="' . $link . '">' . $this->escapeHtml($value) . '</a>';
	}
}
