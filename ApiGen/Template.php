<?php

/**
 * ApiGen 2.4.1 - API documentation generator for PHP 5.3+
 *
 * Copyright (c) 2010-2011 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011-2012 Jaroslav Hanslík (https://github.com/kukulich)
 * Copyright (c) 2011-2012 Ondřej Nešpor (https://github.com/Andrewsville)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace ApiGen;

use Nette, FSHL;

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
		$this->generator = $generator;
		$this->config = $generator->getConfig();

		$that = $this;

		// Output in HTML5
		Nette\Utils\Html::$xhtml = false;

		// FSHL
		$fshl = new FSHL\Highlighter(new FSHL\Output\Html());
		$fshl->setLexer(new FSHL\Lexer\Php());

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
			function($parser, $matches, $name) use ($fshl) {
				if ('code' === $matches[1]) {
					$lines = array_filter(explode("\n", $matches[2]));
					if (!empty($lines)) {
						$firstLine = array_shift($lines);

						$indent = '';
						$li = 0;

						while (isset($firstLine[$li]) && preg_match('~\s~', $firstLine[$li])) {
							foreach ($lines as $line) {
								if (!isset($line[$li]) || $firstLine[$li] !== $line[$li]) {
									break 2;
								}
							}

							$indent .= $firstLine[$li++];
						}

						if (!empty($indent)) {
							$matches[2] = str_replace(
								"\n" . $indent,
								"\n",
								0 === strpos($matches[2], $indent) ? substr($matches[2], $li) : $matches[2]
							);
						}
					}

					$content = $fshl->highlight($matches[2]);
				} else {
					$content = htmlspecialchars($matches[2]);
				}

				$content = $parser->getTexy()->protect($content, \Texy::CONTENT_BLOCK);
				return \TexyHtml::el('pre', $content);
			},
			'~<(code|pre)>(.+?)</\1>~s',
			'codeBlockSyntax'
		);

		$latte = new Nette\Latte\Engine();
		$macroSet = new Nette\Latte\Macros\MacroSet($latte->compiler);
		$macroSet->addMacro('try', 'try {', '} catch (\Exception $e) {}');
		$this->registerFilter($latte);

		// Common operations
		$this->registerHelperLoader('Nette\Templating\DefaultHelpers::loader');

		// PHP source highlight
		$this->registerHelper('highlightPHP', function($source, $context) use ($that, $fshl) {
			return $that->resolveLink($source, $context) ?: $fshl->highlight((string) $source);
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
					$separator = $context instanceof ReflectionClass || !$description ? ' ' : '<br>';
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
			// Unsupported or deprecated annotations
			static $unsupported = array(
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

			// Show/hide tasks
			if (!$todo) {
				unset($annotations['todo']);
			}

			return $annotations;
		});

		$this->registerHelper('annotationSort', function(array $annotations) {
			uksort($annotations, function($one, $two) {
				static $order = array(
					'deprecated' => 0, 'category' => 1, 'package' => 2, 'subpackage' => 3, 'copyright' => 4,
					'license' => 5, 'author' => 6, 'version' => 7, 'since' => 8, 'see' => 9, 'uses' => 10,
					'usedby' => 11, 'link' => 12, 'internal' => 13, 'example' => 14, 'tutorial' => 15, 'todo' => 16
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
	 * @param \ApiGen\ReflectionElement $context
	 * @return string
	 */
	public function getTypeLinks($annotation, ReflectionElement $context)
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
	 * Returns a link to class summary file.
	 *
	 * @param string|\ApiGen\ReflectionClass $class Class reflection or name
	 * @return string
	 */
	public function getClassUrl($class)
	{
		$className = $class instanceof ReflectionClass ? $class->getName() : $class;
		return sprintf($this->config->template['templates']['main']['class']['filename'], $this->urlize($className));
	}

	/**
	 * Returns a link to method in class summary file.
	 *
	 * @param \ApiGen\ReflectionMethod $method Method reflection
	 * @param \ApiGen\ReflectionClass $class Method declaring class
	 * @return string
	 */
	public function getMethodUrl(ReflectionMethod $method, ReflectionClass $class = null)
	{
		$className = null !== $class ? $class->getName() : $method->getDeclaringClassName();
		return $this->getClassUrl($className) . '#_' . $method->getName();
	}

	/**
	 * Returns a link to property in class summary file.
	 *
	 * @param \ApiGen\ReflectionProperty $property Property reflection
	 * @param \ApiGen\ReflectionClass $class Property declaring class
	 * @return string
	 */
	public function getPropertyUrl(ReflectionProperty $property, ReflectionClass $class = null)
	{
		$className = null !== $class ? $class->getName() : $property->getDeclaringClassName();
		return $this->getClassUrl($className) . '#$' . $property->getName();
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
			return $this->getClassUrl($className) . '#' . $constant->getName();
		}
		// Constant in namespace or global space
		return sprintf($this->config->template['templates']['main']['constant']['filename'], $this->urlize($constant->getName()));
	}

	/**
	 * Returns a link to function summary file.
	 *
	 * @param \ApiGen\ReflectionFunction $function Function reflection
	 * @return string
	 */
	public function getFunctionUrl(ReflectionFunction $function)
	{
		return sprintf($this->config->template['templates']['main']['function']['filename'], $this->urlize($function->getName()));
	}

	/**
	 * Returns a link to a element source code.
	 *
	 * @param \ApiGen\ReflectionElement $element Element reflection
	 * @param boolean $withLine Include file line number into the link
	 * @return string
	 */
	public function getSourceUrl(ReflectionElement $element, $withLine = true)
	{
		if ($element instanceof ReflectionClass || $element instanceof ReflectionFunction || ($element instanceof ReflectionConstant && null === $element->getDeclaringClassName())) {
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
	 * @param \ApiGen\ReflectionBase $element Element reflection
	 * @return string
	 */
	public function getManualUrl(ReflectionBase $element)
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
	 * Tries to parse a definition of a class/method/property/constant/function and returns the appropriate link if successful.
	 *
	 * @param string $definition Definition
	 * @param \ApiGen\ReflectionElement $context Link context
	 * @return string|null
	 */
	public function resolveLink($definition, ReflectionElement $context)
	{
		if (empty($definition)) {
			return null;
		}

		$suffix = '';
		if ('[]' === substr($definition, -2)) {
			$definition = substr($definition, 0, -2);
			$suffix = '[]';
		}

		$element = $this->generator->resolveElement($definition, $context);
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

		return sprintf('<code>%s</code>', $link . $suffix);
	}

	/**
	 * Resolves links in documentation.
	 *
	 * @param string $text Processed documentation text
	 * @param \ApiGen\ReflectionElement $context Reflection object
	 * @return string
	 */
	private function resolveLinks($text, ReflectionElement $context)
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
	 * @param \ApiGen\ReflectionElement $context Reflection object
	 * @param boolean $block Parse text as block
	 * @return string
	 */
	public function doc($text, ReflectionElement $context, $block = false)
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
	public function urlize($string)
	{
		return preg_replace('~[^\w]~', '.', $string);
	}
}
