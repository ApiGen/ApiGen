<?php

/**
 * ApiGen 3.0dev - API documentation generator for PHP 5.3+
 *
 * Copyright (c) 2010-2011 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011-2012 Jaroslav Hanslík (https://github.com/kukulich)
 * Copyright (c) 2011-2012 Ondřej Nešpor (https://github.com/Andrewsville)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace ApiGen;

use Nette;

/**
 * Customized ApiGen template class.
 *
 * Adds ApiGen helpers to the Nette\Templating\FileTemplate parent class.
 */
class Template extends Nette\Templating\FileTemplate
{
	/**
	 * Generator.
	 *
	 * @var \ApiGen\Generator
	 */
	private $generator;

	/**
	 * Config.
	 *
	 * @var \ApiGen\Config
	 */
	private $config;

	/**
	 * Markup.
	 *
	 * @var \ApiGen\IMarkup
	 */
	private $markup;

	/**
	 * Creates template.
	 *
	 * @param \ApiGen\Generator $generator
	 * @param \ApiGen\ISourceCodeHighlighter $highlighter
	 */
	public function __construct(Generator $generator, ISourceCodeHighlighter $highlighter)
	{
		$this->generator = $generator;
		$this->config = $generator->getConfig();
		// @todo DI
		$this->markup = new TexyMarkup($this->config->allowedHtml, $highlighter);

		$that = $this;

		// Output in HTML5
		Nette\Utils\Html::$xhtml = false;

		$latte = new Nette\Latte\Engine();
		$macroSet = new Nette\Latte\Macros\MacroSet($latte->compiler);
		$macroSet->addMacro('try', 'try {', '} catch (\Exception $e) {}');
		$this->registerFilter($latte);

		// Common operations
		$this->registerHelperLoader('Nette\Templating\Helpers::loader');

		// PHP source highlight
		$this->registerHelper('highlightPHP', function($source, $context) use ($that, $highlighter) {
			return $that->resolveLink($source, $context) ?: $highlighter->highlight((string) $source);
		});
		$this->registerHelper('highlightValue', function($definition, $context) use ($that) {
			return $that->highlightPHP(preg_replace('~^(?:[ ]{4}|\t)~m', '', $definition), $context);
		});

		// Urls
		$this->registerHelper('packageUrl', new Nette\Callback($this, 'getPackageUrl'));
		$this->registerHelper('namespaceUrl', new Nette\Callback($this, 'getNamespaceUrl'));
		$this->registerHelper('groupUrl', new Nette\Callback($this, 'getGroupUrl'));
		$this->registerHelper('classUrl', new Nette\Callback($this, 'getClassUrl'));
		$this->registerHelper('methodUrl', new Nette\Callback($this, 'getMethodUrl'));
		$this->registerHelper('propertyUrl', new Nette\Callback($this, 'getPropertyUrl'));
		$this->registerHelper('constantUrl', new Nette\Callback($this, 'getConstantUrl'));
		$this->registerHelper('functionUrl', new Nette\Callback($this, 'getFunctionUrl'));
		$this->registerHelper('elementUrl', new Nette\Callback($this, 'getElementUrl'));
		$this->registerHelper('sourceUrl', new Nette\Callback($this, 'getSourceUrl'));
		$this->registerHelper('manualUrl', new Nette\Callback($this, 'getManualUrl'));

		// Packages & namespaces
		$this->registerHelper('packageLinks', new Nette\Callback($this, 'getPackageLinks'));
		$this->registerHelper('namespaceLinks', new Nette\Callback($this, 'getNamespaceLinks'));
		$this->registerHelper('subgroupName', function($groupName) {
			if ($pos = strrpos($groupName, '\\')) {
				return substr($groupName, $pos + 1);
			}
			return $groupName;
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
			if ($context instanceof Reflection\ReflectionParameter) {
				$description = preg_replace('~^(\\$' . $context->getName() . ')(\s+|$)~i', '\\2', $description, 1);
			}
			return $that->doc($description, $context);
		});
		$this->registerHelper('shortDescription', function($element, $block = false) use ($that) {
			return $that->doc($element->getShortDescription(), $element, $block);
		});
		$this->registerHelper('longDescription', function($element) use ($that) {
			$long = $element->getLongDescription();

			// Merge lines
			$long = preg_replace_callback('~(?:<(code|pre)>.+?</\1>)|([^<]*)~s', function($matches) {
				return !empty($matches[2])
					? preg_replace('~\n(?:\t|[ ])+~', ' ', $matches[2])
					: $matches[0];
			}, $long);

			return $that->doc($long, $element, true);
		});

		// Individual annotations processing
		$this->registerHelper('annotation', function($value, $name, $context) use ($that, $generator) {
			switch ($name) {
				case 'param':
				case 'return':
				case 'throws':
					$description = $that->description($value, $context);
					return sprintf('<code>%s</code>%s', $that->getTypeLinks($value, $context), $description ? '<br>' . $description : '');
				case 'license':
					list($url, $description) = $that->split($value);
					return $that->link($url, $description ?: $url);
				case 'link':
					list($url, $description) = $that->split($value);
					if (Nette\Utils\Validators::isUrl($url)) {
						return $that->link($url, $description ?: $url);
					}
					break;
				case 'see':
					$doc = array();
					foreach (preg_split('~\\s*,\\s*~', $value) as $link) {
						if (null !== $generator->resolveElement($link, $context)) {
							$doc[] = sprintf('<code>%s</code>', $that->getTypeLinks($link, $context));
						} else {
							$doc[] = $that->doc($link, $context);
						}
					}
					return implode(', ', $doc);
				case 'uses':
				case 'usedby':
					list($link, $description) = $that->split($value);
					$separator = $context instanceof Reflection\ReflectionClass || !$description ? ' ' : '<br>';
					if (null !== $generator->resolveElement($link, $context)) {
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
			// Filtered, unsupported or deprecated annotations
			static $unsupported = array(
				'package', 'subpackage', 'property', 'property-read', 'property-write', 'method', 'abstract',
				'access', 'final', 'filesource', 'global', 'name', 'static', 'staticvar'
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

			// Show/hide tasks
			if (!$todo) {
				unset($annotations['todo']);
			}

			return $annotations;
		});

		$this->registerHelper('annotationSort', function(array $annotations) {
			uksort($annotations, function($one, $two) {
				static $order = array(
					'deprecated' => 0, 'category' => 1, 'copyright' => 2, 'license' => 3, 'author' => 4, 'version' => 5,
					'since' => 6, 'see' => 7, 'uses' => 8, 'usedby' => 9, 'link' => 10, 'internal' => 11,
					'example' => 12, 'tutorial' => 13, 'todo' => 14
				);

				if (isset($order[$one], $order[$two])) {
					return $order[$one] - $order[$two];
				} elseif (isset($order[$one])) {
					return -1;
				} elseif (isset($order[$two])) {
					return 1;
				} else {
					return strcasecmp($one, $two);
				}
			});
			return $annotations;
		});

		$this->registerHelper('annotationBeautify', function($annotation) {
			static $names = array(
				'usedby' => 'Used by'
			);

			if (isset($names[$annotation])) {
				return $names[$annotation];
			}

			return Nette\Utils\Strings::firstUpper($annotation);
		});

		// Static files versioning
		$destination = $this->config->destination;
		$this->registerHelper('staticFile', function($name) use ($destination) {
			static $versions = array();

			$filename = $destination . DIRECTORY_SEPARATOR . $name;
			if (!isset($versions[$filename]) && is_file($filename)) {
				$versions[$filename] = sprintf('%u', crc32(file_get_contents($filename)));
			}
			if (isset($versions[$filename])) {
				$name .= '?' . $versions[$filename];
			}
			return $name;
		});

		$this->registerHelper('urlize', array($this, 'urlize'));

		$this->registerHelper('relativePath', array($generator, 'getRelativePath'));
		$this->registerHelper('resolveElement', array($generator, 'resolveElement'));
		$this->registerHelper('getClass', array($generator, 'getClass'));
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
	 * @param \ApiGen\Reflection\ReflectionElement $context
	 * @return string
	 */
	public function getTypeLinks($annotation, Reflection\ReflectionElement $context)
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
	 * Returns links for package/namespace and its parent packages.
	 *
	 * @param string $package
	 * @param boolean $last
	 * @return string
	 */
	public function getPackageLinks($package, $last = true)
	{
		if (empty($this->packages)) {
			return $package;
		}

		$links = array();

		$parent = '';
		foreach (explode('\\', $package) as $part) {
			$parent = ltrim($parent . '\\' . $part, '\\');
			$links[] = $last || $parent !== $package
				? $this->link($this->getPackageUrl($parent), $part)
				: $this->escapeHtml($part);
		}

		return implode('\\', $links);
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
		if (empty($this->namespaces)) {
			return $namespace;
		}

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
		return sprintf($this->config->template['templates']['main']['namespace']['filename'], $this->urlize($namespaceName));
	}

	/**
	 * Returns a link to a package summary file.
	 *
	 * @param string $packageName Package name
	 * @return string
	 */
	public function getPackageUrl($packageName)
	{
		return sprintf($this->config->template['templates']['main']['package']['filename'], $this->urlize($packageName));
	}

	/**
	 * Returns a link to a group summary file.
	 *
	 * @param string $groupName Group name
	 * @return string
	 */
	public function getGroupUrl($groupName)
	{
		if (!empty($this->packages)) {
			return $this->getPackageUrl($groupName);
		}

		return $this->getNamespaceUrl($groupName);
	}

	/**
	 * Returns a link to class summary file.
	 *
	 * @param string|\ApiGen\Reflection\ReflectionClass $class Class reflection or name
	 * @return string
	 */
	public function getClassUrl($class)
	{
		$className = $class instanceof Reflection\ReflectionClass ? $class->getName() : $class;
		return sprintf($this->config->template['templates']['main']['class']['filename'], $this->urlize($className));
	}

	/**
	 * Returns a link to method in class summary file.
	 *
	 * @param \ApiGen\Reflection\ReflectionMethod $method Method reflection
	 * @param \ApiGen\Reflection\ReflectionClass $class Method declaring class
	 * @return string
	 */
	public function getMethodUrl(Reflection\ReflectionMethod $method, Reflection\ReflectionClass $class = null)
	{
		$className = null !== $class ? $class->getName() : $method->getDeclaringClassName();
		return $this->getClassUrl($className) . '#_' . $method->getName();
	}

	/**
	 * Returns a link to property in class summary file.
	 *
	 * @param \ApiGen\Reflection\ReflectionProperty $property Property reflection
	 * @param \ApiGen\Reflection\ReflectionClass $class Property declaring class
	 * @return string
	 */
	public function getPropertyUrl(Reflection\ReflectionProperty $property, Reflection\ReflectionClass $class = null)
	{
		$className = null !== $class ? $class->getName() : $property->getDeclaringClassName();
		return $this->getClassUrl($className) . '#$' . $property->getName();
	}

	/**
	 * Returns a link to constant in class summary file or to constant summary file.
	 *
	 * @param \ApiGen\Reflection\ReflectionConstant $constant Constant reflection
	 * @return string
	 */
	public function getConstantUrl(Reflection\ReflectionConstant $constant)
	{
		// Class constant
		if ($className = $constant->getDeclaringClassName()) {
			return $this->getClassUrl($className) . '#' . $constant->getName();
		}
		// Constant in namespace or global space
		return sprintf($this->config->template['templates']['main']['constant']['filename'], $this->urlize($constant->getName()));
	}

	/**
	 * Returns a link to function summary file.
	 *
	 * @param \ApiGen\Reflection\ReflectionFunction $function Function reflection
	 * @return string
	 */
	public function getFunctionUrl(Reflection\ReflectionFunction $function)
	{
		return sprintf($this->config->template['templates']['main']['function']['filename'], $this->urlize($function->getName()));
	}

	/**
	 * Returns a link to element summary file.
	 *
	 * @param \ApiGen\Reflection\ReflectionElement $element Element reflection
	 * @return string
	 */
	public function getElementUrl(Reflection\ReflectionElement $element)
	{
		if ($element instanceof Reflection\ReflectionClass) {
			return $this->getClassUrl($element);
		} elseif ($element instanceof Reflection\ReflectionMethod) {
			return $this->getMethodUrl($element);
		} elseif ($element instanceof Reflection\ReflectionProperty) {
			return $this->getPropertyUrl($element);
		} elseif ($element instanceof Reflection\ReflectionConstant) {
			return $this->getConstantUrl($element);
		} elseif ($element instanceof Reflection\ReflectionFunction) {
			return $this->getFunctionUrl($element);
		}
	}

	/**
	 * Returns a link to a element source code.
	 *
	 * @param \ApiGen\Reflection\ReflectionElement $element Element reflection
	 * @param boolean $withLine Include file line number into the link
	 * @return string
	 */
	public function getSourceUrl(Reflection\ReflectionElement $element, $withLine = true)
	{
		if ($element instanceof Reflection\ReflectionClass || $element instanceof Reflection\ReflectionFunction || ($element instanceof Reflection\ReflectionConstant && null === $element->getDeclaringClassName())) {
			$elementName = $element->getName();

			if ($element instanceof Reflection\ReflectionClass) {
				$file = 'class-';
			} elseif ($element instanceof Reflection\ReflectionConstant) {
				$file = 'constant-';
			} elseif ($element instanceof Reflection\ReflectionFunction) {
				$file = 'function-';
			}
		} else {
			$elementName = $element->getDeclaringClassName();
			$file = 'class-';
		}

		$file .= $this->urlize($elementName);

		$line = null;
		if ($withLine) {
			$line = $element->getStartLine();
			if ($doc = $element->getDocComment()) {
				$line -= substr_count($doc, "\n") + 1;
			}
		}

		return sprintf($this->config->template['templates']['main']['source']['filename'], $file) . (isset($line) ? '#' . $line : '');
	}

	/**
	 * Returns a link to a element documentation at php.net.
	 *
	 * @param \ApiGen\Reflection\ReflectionBase $element Element reflection
	 * @return string
	 */
	public function getManualUrl(Reflection\ReflectionBase $element)
	{
		static $manual = 'http://php.net/manual';
		static $reservedClasses = array('stdClass', 'Closure', 'Directory');

		// Extension
		if ($element instanceof Reflection\ReflectionExtension) {
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
		$class = $element instanceof Reflection\ReflectionClass ? $element : $element->getDeclaringClass();

		if (in_array($class->getName(), $reservedClasses)) {
			return $manual . '/reserved.classes.php';
		}

		$className = strtolower($class->getName());
		$classUrl = sprintf('%s/class.%s.php', $manual, $className);
		$elementName = strtolower(strtr(ltrim($element->getName(), '_'), '_', '-'));

		if ($element instanceof Reflection\ReflectionClass) {
			return $classUrl;
		} elseif ($element instanceof Reflection\ReflectionMethod) {
			return sprintf('%s/%s.%s.php', $manual, $className, $elementName);
		} elseif ($element instanceof Reflection\ReflectionProperty) {
			return sprintf('%s#%s.props.%s', $classUrl, $className, $elementName);
		} elseif ($element instanceof Reflection\ReflectionConstant) {
			return sprintf('%s#%s.constants.%s', $classUrl, $className, $elementName);
		}
	}

	/**
	 * Tries to parse a definition of a class/method/property/constant/function and returns the appropriate link if successful.
	 *
	 * @param string $definition Definition
	 * @param \ApiGen\Reflection\ReflectionElement $context Link context
	 * @return string|null
	 */
	public function resolveLink($definition, Reflection\ReflectionElement $context)
	{
		if (empty($definition)) {
			return null;
		}

		$suffix = '';
		if ('[]' === substr($definition, -2)) {
			$definition = substr($definition, 0, -2);
			$suffix = '[]';
		}

		$element = $this->generator->resolveElement($definition, $context, $expectedName);
		if (null === $element) {
			return $expectedName;
		}

		$classes = array();
		if ($element->isDeprecated()) {
			$classes[] = 'deprecated';
		}
		if (!$element->isValid()) {
			$classes[] = 'invalid';
		}

		if ($element instanceof Reflection\ReflectionClass) {
			$link = $this->link($this->getClassUrl($element), $element->getName(), true, $classes);
		} elseif ($element instanceof Reflection\ReflectionConstant && null === $element->getDeclaringClassName()) {
			$text = $element->inNamespace()
				? $this->escapeHtml($element->getNamespaceName()) . '\\<b>' . $this->escapeHtml($element->getShortName()) . '</b>'
				: '<b>' . $this->escapeHtml($element->getName()) . '</b>';
			$link = $this->link($this->getConstantUrl($element), $text, false, $classes);
		} elseif ($element instanceof Reflection\ReflectionFunction) {
			$link = $this->link($this->getFunctionUrl($element), $element->getName() . '()', true, $classes);
		} else {
			$text = $this->escapeHtml($element->getDeclaringClassName());
			if ($element instanceof Reflection\ReflectionProperty) {
				$url = $this->propertyUrl($element);
				$text .= '::<var>$' . $this->escapeHtml($element->getName()) . '</var>';
			} elseif ($element instanceof Reflection\ReflectionMethod) {
				$url = $this->methodUrl($element);
				$text .= '::' . $this->escapeHtml($element->getName()) . '()';
			} elseif ($element instanceof Reflection\ReflectionConstant) {
				$url = $this->constantUrl($element);
				$text .= '::<b>' . $this->escapeHtml($element->getName()) . '</b>';
			}

			$link = $this->link($url, $text, false, $classes);
		}

		return sprintf('<code>%s</code>', $link . $suffix);
	}

	/**
	 * Resolves links in documentation.
	 *
	 * @param string $text Processed documentation text
	 * @param \ApiGen\Reflection\ReflectionElement $context Reflection object
	 * @return string
	 */
	private function resolveLinks($text, Reflection\ReflectionElement $context)
	{
		$that = $this;
		return preg_replace_callback('~{@(?:link|see)\\s+([^}]+)}~', function ($matches) use ($context, $that) {
			// Texy already added <a> so it has to be stripped
			list($url, $description) = $that->split(strip_tags($matches[1]));
			if (Nette\Utils\Validators::isUrl($url)) {
				return $that->link($url, $description ?: $url);
			}
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
	 * @param \ApiGen\Reflection\ReflectionElement $context Reflection object
	 * @param boolean $block Parse text as block
	 * @return string
	 */
	public function doc($text, Reflection\ReflectionElement $context, $block = false)
	{
		// Resolve @internal
		$text = $this->resolveInternal($text);

		// Process markup
		if ($block) {
			$text = $this->markup->block($text);
		} else {
			$text = $this->markup->line($text);
		}

		// Resolve @link and @see
		$text = $this->resolveLinks($text, $context);

		return $text;
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
	 * @param array $classes List of classes
	 * @return string
	 */
	public function link($url, $text, $escape = true, array $classes = array())
	{
		$class = !empty($classes) ? sprintf(' class="%s"', implode(' ', $classes)) : '';
		return sprintf('<a href="%s"%s>%s</a>', $url, $class, $escape ? $this->escapeHtml($text) : $text);
	}

	/**
	 * Converts string to url safe characters.
	 *
	 * @param string $string
	 * @return string
	 */
	public function urlize($string)
	{
		return preg_replace('~[^\w]~', '.', $string);
	}
}
