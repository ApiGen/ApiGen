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
use Apigen\Reflection as ApiReflection, Apigen\Generator;
use TokenReflection\IReflectionClass as ReflectionClass, TokenReflection\IReflectionProperty as ReflectionProperty, TokenReflection\IReflectionMethod as ReflectionMethod, TokenReflection\IReflectionConstant as ReflectionConstant;
use TokenReflection\ReflectionAnnotation, TokenReflection\ReflectionBase;

class Template extends Nette\Templating\FileTemplate
{
	/**
	 * Generator.
	 *
	 * @var \Apigen\Generator
	 */
	private $generator;

	/**
	 * Creates template.
	 *
	 * @param \Apigen\Generator $generator
	 */
	public function __construct(Generator $generator)
	{
		$this->generator = $generator;

		$this->setCacheStorage(new Nette\Caching\Storages\MemoryStorage);

		$that = $this;

		$latte = new Nette\Latte\Engine;
		$latte->handler->macros['try'] = '<?php try { ?>';
		$latte->handler->macros['/try'] = '<?php } catch (\Exception $e) {} ?>';
		$this->registerFilter($latte);

		// common operations
		$this->registerHelperLoader('Nette\Templating\DefaultHelpers::loader');
		$this->registerHelper('ucfirst', 'ucfirst');
		$this->registerHelper('values', 'array_values');
		$this->registerHelper('map', function($arr, $callback) {
			return array_map(create_function('$value', $callback), $arr);
		});
		$this->registerHelper('replaceRE', 'Nette\StringUtils::replace');

		// PHP source highlight
		$fshl = new \fshlParser('HTML_UTF8');
		$this->registerHelper('highlightPHP', function($source) use ($fshl) {
			return $fshl->highlightString('PHP', (string) $source);
		});

		// links
		$this->registerHelper('packageLink', callback($this->generator, 'getPackageLink'));
		$this->registerHelper('namespaceLink', callback($this->generator, 'getNamespaceLink'));
		$this->registerHelper('classLink', callback($this->generator, 'getClassLink'));
		$this->registerHelper('methodLink', callback($this->generator, 'getMethodLink'));
		$this->registerHelper('propertyLink', callback($this->generator, 'getPropertyLink'));
		$this->registerHelper('constantLink', callback($this->generator, 'getConstantLink'));
		$this->registerHelper('sourceLink', callback($this->generator, 'getSourceLink'));

		// types
		$this->registerHelper('getTypes', callback($this, 'getTypes'));
		$this->registerHelper('getType', function($variable) {
			return is_object($variable) ? get_class($variable) : gettype($variable);
		});
		$this->registerHelper('resolveType', callback($this, 'resolveType'));

		// docblock
		$texy = new \Texy;
		$texy->mergeLines = FALSE;
		$texy->allowedTags = \Texy::NONE;
		$texy->allowed['list/definition'] = FALSE;
		$texy->allowed['phrase/em-alt'] = FALSE;
		$texy->allowed['longwords'] = FALSE;
		// highlighting <code>, <pre>
		$texy->registerBlockPattern(
			function($parser, $matches, $name) use ($fshl) {
				$content = $matches[1] === 'code' ? $fshl->highlightString('PHP', $matches[2]) : htmlSpecialChars($matches[2]);
				$content = $parser->getTexy()->protect($content, \Texy::CONTENT_BLOCK);
				return \TexyHtml::el('pre', $content);
			},
			'#<(code|pre)>(.+?)</\1>#s',
			'codeBlockSyntax'
		);
		// {@link ...} resolving
		$texy->registerLinePattern(
			function($parser, $matches) use ($that) {
				$link = $that->resolveClassLink($matches[1], $that->class);
				return null === $link ? $matches[0] : $parser->getTexy()->protect($link, \Texy::CONTENT_BLOCK);
			},
			'~{@link\\s+([^}]+)}~',
			'resolveLinks'
		);

		// Documentation formatting
		$this->registerHelper('docline', function($text) use ($texy) {
			return $texy->processLine($text);
		});
		$this->registerHelper('docblock', function($text) use ($texy) {
			return $texy->process($text);
		});
		$this->registerHelper('doclabel', function($doc, $namespace) use ($that) {
			@list($names, $label) = preg_split('#\s+#', $doc, 2);
			$res = '';
			foreach (explode('|', $names) as $name) {
				$class = $that->resolveType($name, $namespace);
				$res .= $class !== null ? sprintf('<a href="%s">%s</a>', $that->classLink($class), $that->escapeHtml($class)) : $that->escapeHtml(ltrim($name, '\\'));
				$res .= '|';
			}
			return rtrim($res, '|') . ' ' . $that->escapeHtml($label);
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

		// individual annotations processing
		$this->registerHelper('annotation', function($value, $name, ApiReflection $parent) use ($that) {
			switch ($name) {
				case 'package':
					return '<a href="' . $that->packageLink($value) . '">' . $that->escapeHtml($value) . '</a>';
					break;
				case 'see':
					return $that->resolveClassLink($value, $parent) ?: $that->docline($value);
					break;
				default:
					return $that->docline($value);
			}


		});

		// static files versioning
		$destination = $this->generator->config->destination;
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
	}

	public function getTypes($element, $position = NULL)
	{
		$annotation = array();
		if ($element instanceof ReflectionProperty) {
			$annotation = $element->getAnnotation('var');
			if (null === $annotation && !$element->isTokenized()) {
				$value = $element->getDefaultValue();
				if (null !== $value) {
					$annotation = gettype($value);
				}
			}
		} elseif ($element instanceof ReflectionMethod) {
			$annotation = $position === null ? $element->getAnnotation('return') : @$element->annotations['param'][$position];
		}

		$namespace = $element->getDeclaringClass()->getNamespaceName();
		$types = array();
		foreach (preg_replace('#\s.*#', '', (array) $annotation) as $s) {
			foreach (explode('|', $s) as $name) {
				$class = $this->resolveType($name, $namespace);
				$types[] = (object) array('name' => $class ?: ltrim($name, '\\'), 'class' => $class);
			}
		}
		return $types;
	}

	/**
	 * Tries to resolve type as class or interface name.
	 *
	 * @param string Data type description
	 * @param string Namespace name
	 * @return string
	 */
	public function resolveType($type, $namespace = NULL)
	{
		if (substr($type, 0, 1) === '\\') {
			$namespace = '';
			$type = substr($type, 1);
		}
		return isset($this->generator->classes["$namespace\\$type"]) ? "$namespace\\$type" : (isset($this->generator->classes[$type]) ? $type : NULL);
	}

	/**
	 * Tries to parse a link to a class/method/property and returns the appropriate link if successful.
	 *
	 * @param string $link Link definition
	 * @param \Apigen\Reflection $context Link context
	 * @return \Apigen\Reflection|\TokenReflection\IReflection|null
	 */
	public function resolveClassLink($link, ApiReflection $context)
	{
		if (($pos = strpos($link, '::')) || ($pos = strpos($link, '->'))) {
			// Class::something or Class->something
			$className = $this->resolveType(substr($link, 0, $pos), $context->getNamespaceName());

			if (null === $className) {
				$className = $this->resolveType(ReflectionBase::resolveClassFQN(substr($link, 0, $pos), $context->getNamespaceAliases(), $context->getNamespaceName()));
			}

			if (null === $className) {
				return null;
			} else {
				$context = $this->generator->classes[$className];
			}

			$link = substr($link, $pos + 2);
		} elseif (null !== ($className = $this->resolveType(ReflectionBase::resolveClassFQN($link, $context->getNamespaceAliases(), $context->getNamespaceName()), $context->getNamespaceName()))
			|| null !== ($className = $this->resolveType($link, $context->getNamespaceName()))) {
			// Class
			return '<a href="' . $this->classLink($this->generator->classes[$className]) . '">' . $this->escapeHtml($className) . '</a>';
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
		} elseif (('()' === substr($link, -2) && $context->hasMethod(substr($link, 0, -2)))) {
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
			$link = $this->propertyLink($reflection);
			$value .= '::$' . $reflection->getName();
		} elseif ($reflection instanceof ReflectionMethod) {
			$link = $this->methodLink($reflection);
			$value .= '::' . $reflection->getName() . '()';
		} elseif ($reflection instanceof ReflectionConstant) {
			$link = $this->constantLink($reflection);
			$value .= '::' . $reflection->getName();
		}

		return '<a href="' . $link . '">' . $this->escapeHtml($value) . '</a>';
	}
}
