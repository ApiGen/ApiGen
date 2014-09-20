<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Templating;

use ApiGen\Configuration\Configuration;
use ApiGen\Generator\Generator;
use ApiGen\Generator\Markups\Markup;
use ApiGen\Generator\SourceCodeHighlighter;
use ApiGen\Reflection\ReflectionBase;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionConstant;
use ApiGen\Reflection\ReflectionElement;
use ApiGen\Reflection\ReflectionExtension;
use ApiGen\Reflection\ReflectionFunction;
use ApiGen\Reflection\ReflectionMethod;
use ApiGen\Reflection\ReflectionProperty;
use Nette;


/**
 * Customized ApiGen template class.
 * Adds ApiGen helpers to the Nette\Templating\FileTemplate parent class.
 *
 * @method Template setGenerator(object)
 */
class Template extends Nette\Templating\FileTemplate
{

	/**
	 * @var Generator
	 */
	private $generator;

	/**
	 * @var Markup
	 */
	private $markup;

	/**
	 * @var SourceCodeHighlighter
	 */
	private $highlighter;

	/**
	 * @var Configuration
	 */
	private $configuration;


	public function __construct(Markup $markup, SourceCodeHighlighter $highlighter, Configuration $configuration)
	{
		$this->markup = $markup;
		$this->highlighter = $highlighter;
		$this->configuration = $configuration;
	}


	public function setup()
	{
		$markup = $this->markup;
		$highlighter = $this->highlighter;
		$generator = $this->generator;

		$that = $this;

		// Output in HTML5
		Nette\Utils\Html::$xhtml = FALSE;

		// Common operations
		$this->registerHelperLoader('Nette\Templating\Helpers::loader');

		// PHP source highlight
		$this->registerHelper('highlightPHP', function ($source, $context) use ($that, $highlighter) {
			return $that->resolveLink($that->getTypeName($source), $context) ?: $highlighter->highlight((string) $source);
		});
		$this->registerHelper('highlightValue', function ($definition, $context) use ($that) {
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
		$this->registerHelper('subgroupName', function ($groupName) {
			if ($pos = strrpos($groupName, '\\')) {
				return substr($groupName, $pos + 1);
			}
			return $groupName;
		});

		// Types
		$this->registerHelper('typeLinks', new Nette\Callback($this, 'getTypeLinks'));

		// Docblock descriptions
		$this->registerHelper('description', function ($annotation, $context) use ($that) {
			$description = trim(strpbrk($annotation, "\n\r\t $")) ?: $annotation;
			return $that->doc($description, $context);
		});
		$this->registerHelper('shortDescription', function ($element, $block = FALSE) use ($that) {
			return $that->doc($element->getShortDescription(), $element, $block);
		});
		$this->registerHelper('longDescription', function ($element) use ($that) {
			$long = $element->getLongDescription();

			// Merge lines
			$long = preg_replace_callback('~(?:<(code|pre)>.+?</\1>)|([^<]*)~s', function ($matches) {
				return ! empty($matches[2])
					? preg_replace('~\n(?:\t|[ ])+~', ' ', $matches[2])
					: $matches[0];
			}, $long);

			return $that->doc($long, $element, TRUE);
		});

		// Individual annotations processing
		$this->registerHelper('annotation', function ($value, $name, ReflectionElement $context) use ($that, $generator) {
			switch ($name) {
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
						if ($generator->resolveElement($link, $context)  !== NULL) {
							$doc[] = sprintf('<code>%s</code>', $that->getTypeLinks($link, $context));

						} else {
							$doc[] = $that->doc($link, $context);
						}
					}
					return implode(', ', $doc);
				case 'uses':
				case 'usedby':
					list($link, $description) = $that->split($value);
					$separator = $context instanceof ReflectionClass || ! $description ? ' ' : '<br>';
					if ($generator->resolveElement($link, $context) !== NULL) {
						return sprintf('<code>%s</code>%s%s', $that->getTypeLinks($link, $context), $separator, $description);
					}
					break;
				default:
					break;
			}

			// Default
			return $that->doc($value, $context);
		});

		$todo = $this->configuration->todo;
		$internal = $this->configuration->internal;
		$this->registerHelper('annotationFilter', function (array $annotations, array $filter = array()) use ($todo, $internal) {
			// Filtered, unsupported or deprecated annotations
			static $filtered = array(
				'package', 'subpackage', 'property', 'property-read', 'property-write', 'method', 'abstract',
				'access', 'final', 'filesource', 'global', 'name', 'static', 'staticvar'
			);
			foreach ($filtered as $annotation) {
				unset($annotations[$annotation]);
			}

			// Custom filter
			foreach ($filter as $annotation) {
				unset($annotations[$annotation]);
			}

			// Show/hide internal
			if ( ! $internal) {
				unset($annotations['internal']);
			}

			// Show/hide tasks
			if ( ! $todo) {
				unset($annotations['todo']);
			}

			return $annotations;
		});

		$this->registerHelper('annotationSort', function (array $annotations) {
			uksort($annotations, function ($one, $two) {
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

		$this->registerHelper('annotationBeautify', function ($annotation) {
			static $names = array(
				'usedby' => 'Used by'
			);

			if (isset($names[$annotation])) {
				return $names[$annotation];
			}

			return Nette\Utils\Strings::firstUpper($annotation);
		});

		// Static files versioning
		$destination = $this->configuration->destination;
		$this->registerHelper('staticFile', function ($name) use ($destination) {
			static $versions = array();

			$filename = $destination . DIRECTORY_SEPARATOR . $name;
			if ( ! isset($versions[$filename]) && is_file($filename)) {
				$versions[$filename] = sprintf('%u', crc32(file_get_contents($filename)));
			}
			if (isset($versions[$filename])) {
				$name .= '?' . $versions[$filename];
			}
			return $name;
		});

		// Source anchors
		$this->registerHelper('sourceAnchors', function ($source) {
			// Classes, interfaces, traits and exceptions
			$source = preg_replace_callback('~(<span\\s+class="php-keyword1">(?:class|interface|trait)</span>\\s+)(\\w+)~i', function ($matches) {
				$link = sprintf('<a id="%1$s" href="#%1$s">%1$s</a>', $matches[2]);
				return $matches[1] . $link;
			}, $source);

			// Methods and functions
			$source = preg_replace_callback('~(<span\\s+class="php-keyword1">function</span>\\s+)(\\w+)~i', function ($matches) {
				$link = sprintf('<a id="_%1$s" href="#_%1$s">%1$s</a>', $matches[2]);
				return $matches[1] . $link;
			}, $source);

			// Constants
			$source = preg_replace_callback('~(<span class="php-keyword1">const</span>)(.*?)(;)~is', function ($matches) {
				$links = preg_replace_callback('~(\\s|,)([A-Z_]+)(\\s+=)~', function ($matches) {
					return $matches[1] . sprintf('<a id="%1$s" href="#%1$s">%1$s</a>', $matches[2]) . $matches[3];
				}, $matches[2]);
				return $matches[1] . $links . $matches[3];
			}, $source);

			// Properties
			$source = preg_replace_callback('~(<span\\s+class="php-keyword1">(?:private|protected|public|var|static)</span>\\s+)(<span\\s+class="php-var">.*?)(;)~is', function ($matches) {
				$links = preg_replace_callback('~(<span\\s+class="php-var">)(\\$\\w+)~i', function ($matches) {
					return $matches[1] . sprintf('<a id="%1$s" href="#%1$s">%1$s</a>', $matches[2]);
				}, $matches[2]);
				return $matches[1] . $links . $matches[3];
			}, $source);

			return $source;
		});

		$this->registerHelper('urlize', array($this, 'urlize'));

		$this->registerHelper('relativePath', array($generator, 'getRelativePath'));
		$this->registerHelper('resolveElement', array($generator, 'resolveElement'));
		$this->registerHelper('getClass', array($generator, 'getClass'));
		$this->sourceCodeHighlighter = $highlighter;
		$this->markup = $markup;
	}


	/**
	 * Returns unified type value definition (class name or member data type).
	 *
	 * @param string $name
	 * @param boolean $trimNamespaceSeparator
	 * @return string
	 */
	public function getTypeName($name, $trimNamespaceSeparator = TRUE)
	{
		static $names = array(
			'int' => 'integer',
			'bool' => 'boolean',
			'double' => 'float',
			'void' => '',
			'FALSE' => 'false',
			'TRUE' => 'true',
			'NULL' => 'null',
			'callback' => 'callable'
		);

		// Simple type
		if (isset($names[$name])) {
			return $names[$name];
		}

		// Class, constant or function
		return $trimNamespaceSeparator ? ltrim($name, '\\') : $name;
	}


	/**
	 * Returns links for types.
	 *
	 * @param string $annotation
	 * @param ReflectionElement $context
	 * @return string
	 */
	public function getTypeLinks($annotation, ReflectionElement $context)
	{
		$links = array();

		list($types) = $this->split($annotation);
		if ( ! empty($types) && $types{0} === '$') {
			$types = NULL;
		}

		if (empty($types)) {
			$types = 'mixed';
		}

		foreach (explode('|', $types) as $type) {
			$type = $this->getTypeName($type, FALSE);
			$links[] = $this->resolveLink($type, $context) ?: $this->escapeHtml(ltrim($type, '\\'));
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
	public function getPackageLinks($package, $last = TRUE)
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
	public function getNamespaceLinks($namespace, $last = TRUE)
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
		return sprintf($this->configuration->template['templates']['main']['namespace']['filename'], $this->urlize($namespaceName));
	}


	/**
	 * Returns a link to a package summary file.
	 *
	 * @param string $packageName Package name
	 * @return string
	 */
	public function getPackageUrl($packageName)
	{
		return sprintf($this->configuration->template['templates']['main']['package']['filename'], $this->urlize($packageName));
	}


	/**
	 * Returns a link to a group summary file.
	 *
	 * @param string $groupName Group name
	 * @return string
	 */
	public function getGroupUrl($groupName)
	{
		if ( ! empty($this->packages)) {
			return $this->getPackageUrl($groupName);
		}

		return $this->getNamespaceUrl($groupName);
	}


	/**
	 * Returns a link to class summary file.
	 *
	 * @param string|ReflectionClass $class Class reflection or name
	 * @return string
	 */
	public function getClassUrl($class)
	{
		$className = $class instanceof ReflectionClass ? $class->getName() : $class;
		return sprintf($this->configuration->template['templates']['main']['class']['filename'], $this->urlize($className));
	}


	/**
	 * Returns a link to method in class summary file.
	 *
	 * @return string
	 */
	public function getMethodUrl(ReflectionMethod $method, ReflectionClass $class = NULL)
	{
		$className = $class !== NULL ? $class->getName() : $method->getDeclaringClassName();
		return $this->getClassUrl($className) . '#' . ($method->isMagic() ? 'm' : '') . '_' . ($method->getOriginalName() ?: $method->getName());
	}


	/**
	 * Returns a link to property in class summary file.
	 *
	 * @return string
	 */
	public function getPropertyUrl(ReflectionProperty $property, ReflectionClass $class = NULL)
	{
		$className = $class !== NULL ? $class->getName() : $property->getDeclaringClassName();
		return $this->getClassUrl($className) . '#' . ($property->isMagic() ? 'm' : '') . '$' . $property->getName();
	}


	/**
	 * Returns a link to constant in class summary file or to constant summary file.
	 *
	 * @param \ApiGen\Reflection\ReflectionConstant $constant Constant reflection
	 * @return string
	 */
	public function getConstantUrl(ReflectionConstant $constant)
	{
		// Class constant
		if ($className = $constant->getDeclaringClassName()) {
			return $this->getClassUrl($className) . '#' . $constant->getName();
		}
		// Constant in namespace or global space
		return sprintf($this->configuration->template['templates']['main']['constant']['filename'], $this->urlize($constant->getName()));
	}


	/**
	 * Returns a link to function summary file.
	 *
	 * @param \ApiGen\ReflectionFunction $function Function reflection
	 * @return string
	 */
	public function getFunctionUrl(ReflectionFunction $function)
	{
		return sprintf($this->configuration->template['templates']['main']['function']['filename'], $this->urlize($function->getName()));
	}


	/**
	 * Returns a link to element summary file.
	 *
	 * @return string
	 */
	public function getElementUrl(ReflectionElement $element)
	{
		if ($element instanceof ReflectionClass) {
			return $this->getClassUrl($element);

		} elseif ($element instanceof ReflectionMethod) {
			return $this->getMethodUrl($element);

		} elseif ($element instanceof ReflectionProperty) {
			return $this->getPropertyUrl($element);

		} elseif ($element instanceof ReflectionConstant) {
			return $this->getConstantUrl($element);

		} elseif ($element instanceof ReflectionFunction) {
			return $this->getFunctionUrl($element);
		}
	}


	/**
	 * Returns a link to a element source code.
	 *
	 * @param ReflectionElement $element
	 * @param boolean $withLine Include file line number into the link
	 * @return string
	 */
	public function getSourceUrl(ReflectionElement $element, $withLine = TRUE)
	{
		if ($element instanceof ReflectionClass || $element instanceof ReflectionFunction
			|| ($element instanceof ReflectionConstant && $element->getDeclaringClassName() === NULL)
		) {
			$elementName = $element->getName();

			if ($element instanceof ReflectionClass) {
				$file = 'class-';

			} elseif ($element instanceof ReflectionConstant) {
				$file = 'constant-';

			} elseif ($element instanceof ReflectionFunction) {
				$file = 'function-';
			}

		} else {
			$elementName = $element->getDeclaringClassName();
			$file = 'class-';
		}

		$file .= $this->urlize($elementName);

		$lines = NULL;
		if ($withLine) {
			$lines = $element->getStartLine() !== $element->getEndLine() ? sprintf('%s-%s', $element->getStartLine(), $element->getEndLine()) : $element->getStartLine();
		}

		return sprintf($this->configuration->template['templates']['main']['source']['filename'], $file) . (NULL !== $lines ? '#' . $lines : '');
	}


	/**
	 * Returns a link to a element documentation at php.net.
	 *
	 * @return string
	 */
	public function getManualUrl(ReflectionBase $element)
	{
		static $manual = 'http://php.net/manual';
		static $reservedClasses = array('stdClass', 'Closure', 'Directory');

		// Extension
		if ($element instanceof ReflectionExtension) {
			$extensionName = strtolower($element->getName());
			if ($extensionName === 'core') {
				return $manual;
			}

			if ($extensionName === 'date') {
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
	 * Tries to parse a definition of a class/method/property/constant/function and returns the appropriate link if successful.
	 *
	 * @param string $definition Definition
	 * @param ReflectionElement $context Link context
	 * @return string|null
	 */
	public function resolveLink($definition, ReflectionElement $context)
	{
		if (empty($definition)) {
			return NULL;
		}

		$suffix = '';
		if (substr($definition, -2) === '[]') {
			$definition = substr($definition, 0, -2);
			$suffix = '[]';
		}

		$element = $this->generator->resolveElement($definition, $context, $expectedName);
		if ($element === NULL) {
			return $expectedName;
		}

		$classes = array();
		if ($element->isDeprecated()) {
			$classes[] = 'deprecated';
		}
		if ( ! $element->isValid()) {
			$classes[] = 'invalid';
		}

		if ($element instanceof ReflectionClass) {
			$link = $this->link($this->getClassUrl($element), $element->getName(), TRUE, $classes);

		} elseif ($element instanceof ReflectionConstant && $element->getDeclaringClassName() === NULL) {
			$text = $element->inNamespace()
				? $this->escapeHtml($element->getNamespaceName()) . '\\<b>' . $this->escapeHtml($element->getShortName()) . '</b>'
				: '<b>' . $this->escapeHtml($element->getName()) . '</b>';
			$link = $this->link($this->getConstantUrl($element), $text, FALSE, $classes);

		} elseif ($element instanceof ReflectionFunction) {
			$link = $this->link($this->getFunctionUrl($element), $element->getName() . '()', TRUE, $classes);

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

			$link = $this->link($url, $text, FALSE, $classes);
		}

		return sprintf('<code>%s</code>', $link . $suffix);
	}


	/**
	 * Resolves links in documentation.
	 *
	 * @param string $text Processed documentation text
	 * @param ReflectionElement $context Reflection object
	 * @return string
	 */
	private function resolveLinks($text, ReflectionElement $context)
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
		$internal = $this->configuration->internal;
		return preg_replace_callback('~\\{@(\\w+)(?:(?:\\s+((?>(?R)|[^{}]+)*)\\})|\\})~', function ($matches) use ($internal) {
			// Replace only internal
			if ($matches[1] !== 'internal') {
				return $matches[0];
			}
			return $internal && isset($matches[2]) ? $matches[2] : '';
		}, $text);
	}


	/**
	 * Formats text as documentation block or line.
	 *
	 * @param string $text Text
	 * @param ReflectionElement $context Reflection object
	 * @param boolean $block Parse text as block
	 * @return string
	 */
	public function doc($text, ReflectionElement $context, $block = FALSE)
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
	public function link($url, $text, $escape = TRUE, array $classes = array())
	{
		$class = ! empty($classes) ? sprintf(' class="%s"', implode(' ', $classes)) : '';
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
