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
use Apigen\Reflection as ApiReflection, Apigen\Exception, Apigen\Config;
use TokenReflection\Broker, Apigen\Backend;
use TokenReflection\IReflectionClass as ReflectionClass, TokenReflection\IReflectionProperty as ReflectionProperty, TokenReflection\IReflectionMethod as ReflectionMethod, TokenReflection\IReflectionConstant as ReflectionConstant;
use TokenReflection\ReflectionAnnotation, TokenReflection\Dummy\ReflectionClass as DummyClass;


/**
 * Generates a HTML API documentation.
 *
 * @author David Grudl
 * @author Ondřej Nešpor
 */
class Generator extends Nette\Object
{
	/**
	 * Library version.
	 *
	 * @var string
	 */
	const VERSION = '2.0 alpha';

	/**
	 * Configuration.
	 *
	 * @var \Apigen\Config
	 */
	private $config;

	/**
	 * Progressbar
	 *
	 * @var \Console_ProgressBar
	 */
	private $progressBar;

	/**
	 * Array of reflection envelopes.
	 *
	 * @var array
	 */
	private $classes = array();

	/**
	 * Sets configuration.
	 *
	 * @param array $config
	 */
	public function __construct(Config $config)
	{
		$this->config = $config;
	}

	/**
	 * Scans and parses PHP files.
	 *
	 * @return array
	 */
	public function parse()
	{
		$broker = new Broker(new Backend(), false);

		$files = array();
		foreach ((array) $this->config['source'] as $source) {
			foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source)) as $entry) {
				if ($entry->isFile() && 'php' === $entry->getExtension()) {
					$files[] = $entry->getPathName();
				}
			}
		}

		if ($this->config['progressbar']) {
			$this->prepareProgressBar(count($files));
		}

		foreach ($files as $file) {
			$broker->processFile($file);
			$this->incrementProgressBar();
		}

		$tokenized = $broker->getClasses(Backend::TOKENIZED_CLASSES);
		$internal = $broker->getClasses(Backend::INTERNAL_CLASSES);

		$that = $this;
		$this->classes = array_map(function(ReflectionClass $class) use($that) {
			return new ApiReflection($class, $that);
		}, array_merge($tokenized, $internal));

		return array(count($tokenized), count($internal));
	}

	/**
	 * Returns configuration.
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function getConfig($name = null)
	{
		if (null === $name) {
			return $this->config;
		}
		return $this->config[$name];
	}

	/**
	 * Returns parsed class list.
	 *
	 * @return array
	 */
	public function getClasses()
	{
		return $this->classes;
	}

	/**
	 * Wipes out the destination directory.
	 *
	 * @return boolean
	 */
	public function wipeOutDestination()
	{
		// resources
		foreach ($this->config['resources'] as $dir) {
			$pathName = $this->config['destination'] . '/' . $dir;
			if (is_dir($pathName)) {
				foreach (Nette\Utils\Finder::findFiles('*')->from($pathName)->childFirst() as $item) {
					if ($item->isDir()) {
						if (!@rmdir($item)) {
							return false;
						}
					} elseif ($item->isFile()) {
						if (!@unlink($item)) {
							return false;
						}
					}
				}
				if (!@rmdir($pathName)) {
					return false;
				}
			}
		}

		// common files
		$filenames = array_keys($this->config['templates']['common']);
		foreach (Nette\Utils\Finder::findFiles($filenames)->from($this->config['destination']) as $item) {
			if (!@unlink($item)) {
				return false;
			}
		}

		// output files
		$masks = array_map(function($mask) {
			return preg_replace('~%[^%]*?s~', '*', $mask);
		}, $this->config['filenames']);
		$filter = function($item) use($masks) {
			foreach ($masks as $mask) {
				if (fnmatch($mask, $item->getFilename())) {
					return true;
				}
			}

			return false;
		};

		foreach (Nette\Utils\Finder::findFiles('*')->filter($filter)->from($this->config['destination']) as $item) {
			if (!@unlink($item)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Generates API documentation.
	 */
	public function generate()
	{
		@mkdir($this->config['destination']);
		if (!is_dir($this->config['destination'])) {
			throw new Exception("Directory {$this->config['destination']} doesn't exist.", Exception::INVALID_CONFIG);
		}

		$destination = $this->config['destination'];
		$templatePath = $this->config['templateDir'] . '/' . $this->config['template'];

		// copy resources
		foreach ($this->config['resources'] as $source => $dest) {
			foreach ($iterator = Nette\Utils\Finder::findFiles('*')->from($templatePath . '/' . $source)->getIterator() as $foo) {
				copy($iterator->getPathName(), self::forceDir("$destination/$dest/" . $iterator->getSubPathName()));
			}
		}

		// categorize by namespaces
		$packages = array();
		$namespaces = array();
		$allClasses = array();
		foreach ($this->classes as $class) {
			$packages[$class->getPackageName()]['classes'][$class->getName()] = $class;
			if ($class->inNamespace()) {
				$packages[$class->getPackageName()]['namespaces'][$class->getNamespaceName()] = true;
				$namespaces[$class->getNamespaceName()]['classes'][$class->getShortName()] = $class;
				$namespaces[$class->getNamespaceName()]['packages'][$class->getPackageName()] = true;
			}
			$allClasses[$class->getName()] = $class;
		}
		uksort($packages, 'strcasecmp');
		uksort($namespaces, 'strcasecmp');
		uksort($allClasses, 'strcasecmp');

		if ($this->config['progressbar']) {
			$this->prepareProgressBar(
				count($allClasses)
				+ count($namespaces)
				+ count($packages)
				+ count($this->config['templates']['common'])
				+ count(array_filter(array_unique(array_map(function(ApiReflection $class) {
					return $class->getFileName();
				}, $allClasses))))
			);
		}

		$template = $this->createTemplate();
		$template->version = self::VERSION;
		foreach ($this->config as $key => $value) {
			$template->$key = $value;
		}

		// generate summary files
		$template->namespaces = array_keys($namespaces);
		$template->packages = array_keys($packages);
		$template->classes = array_filter($allClasses, function($class) {
			return !$class->isInterface() && !$class->isException();
		});
		$template->interfaces = array_filter($allClasses, function($class) {
			return $class->isInterface() && !$class->isException();
		});
		$template->exceptions = array_filter($allClasses, function($class) {
			return $class->isException();
		});
		foreach ($this->config['templates']['common'] as $dest => $source) {
			$template->setFile($templatePath . '/' . $source)->save(self::forceDir("$destination/$dest"));

			$this->incrementProgressBar();
		}

		$generatedFiles = array();
		$fshl = new \fshlParser('HTML_UTF8', P_TAB_INDENT | P_LINE_COUNTER);

		// generate namespace summary
		$template->package = null;
		foreach ($namespaces as $namespace => $definition) {
			$classes = isset($definition['classes']) ? $definition['classes'] : array();
			uksort($classes, 'strcasecmp');
			$nPackages = isset($definition['packages']) ? array_keys($definition['packages']) : array();
			usort($nPackages, 'strcasecmp');
			$template->package = 1 === count($nPackages) ? $nPackages[0] : null;
			$template->packages = $nPackages;
			$template->namespace = $namespace;
			$template->namespaces = array_filter(array_keys($namespaces), function($item) use($namespace) {
				return strpos($item, $namespace) === 0 || strpos($namespace, $item) === 0;
			});
			$template->classes = array_filter($classes, function($class) {
				return !$class->isInterface() && !$class->isException();
			});
			$template->interfaces = array_filter($classes, function($class) {
				return $class->isInterface() && !$class->isException();
			});
			$template->exceptions = array_filter($classes, function($class) {
				return $class->isException();
			});
			$template->setFile($templatePath . '/' . $this->config['templates']['namespace'])->save(self::forceDir($destination . '/' . $this->formatNamespaceLink($namespace)));

			$this->incrementProgressBar();
		}

		// generate package summary
		$template->namespace = null;
		foreach ($packages as $package => $definition) {
			$classes = isset($definition['classes']) ? $definition['classes'] : array();
			uksort($classes, 'strcasecmp');
			$pNamespaces = isset($definition['namespaces']) ? array_keys($definition['namespaces']) : array();
			usort($pNamespaces, 'strcasecmp');
			$template->package = $package;
			$template->packages = array($package);
			$template->namespaces = $pNamespaces;
			$template->classes = array_filter($classes, function($class) {
				return !$class->isInterface() && !$class->isException();
			});
			$template->interfaces = array_filter($classes, function($class) {
				return $class->isInterface() && !$class->isException();
			});
			$template->exceptions = array_filter($classes, function($class) {
				return $class->isException();
			});
			$template->setFile($templatePath . '/' . $this->config['templates']['package'])->save(self::forceDir($destination . '/' . $this->formatPackageLink($package)));

			$this->incrementProgressBar();
		}


		// generate class & interface files
		$template->classes = $allClasses;
		foreach ($allClasses as $class) {
			$template->package = $package = $class->getPackageName();
			$template->namespace = $namespace = $class->getNamespaceName();
			if ($namespace) {
				$template->namespaces = array_filter(array_keys($namespaces), function($item) use($namespace) {
					return strpos($item, $namespace) === 0 || strpos($namespace, $item) === 0;
				});
			} else {
				$template->namespaces = array();
			}
			$template->packages = array($package);
			$template->tree = array($class);
			while ($parent = $template->tree[0]->getParentClass()) {
				array_unshift($template->tree, $parent);
			}
			$template->classes = !$class->isInterface() && !$class->isException() ? array($class) : array();
			$template->interfaces = $class->isInterface() && !$class->isException() ? array($class) : array();
			$template->exceptions = $class->isException() ? array($class) : array();

			$template->directSubClasses = $class->getDirectSubClasses();
			uksort($template->directSubClasses, 'strcasecmp');
			$template->indirectSubClasses = $class->getIndirectSubClasses();
			uksort($template->indirectSubClasses, 'strcasecmp');

			$template->directImplementers = $class->getDirectImplementers();
			uksort($template->directImplementers, 'strcasecmp');
			$template->indirectImplementers = $class->getIndirectImplementers();
			uksort($template->indirectImplementers, 'strcasecmp');

			if ($class->isTokenized()) {
				$template->fileName = null;
				$file = $class->getFileName();
				foreach ($this->config['source'] as $source) {
					if (0 === strpos($file, $source)) {
						$template->fileName = str_replace('\\', '/', substr($file, strlen($source) + 1));
						break;
					}
				}
				if (null === $template->fileName) {
					throw new Exception(sprintf('Could not determine class %s relative path', $class->getName()));
				}
			}

			$template->class = $class;
			$template->setFile($templatePath . '/' . $this->config['templates']['class'])->save(self::forceDir($destination . '/' . $this->formatClassLink($class)));

			$this->incrementProgressBar();

			// generate source codes
			if ($class->isUserDefined() && !isset($generatedFiles[$class->getFileName()])) {
				$template->source = $fshl->highlightString('PHP', file_get_contents($file));
				$template->setFile($templatePath . '/' . $this->config['templates']['source'])->save(self::forceDir($destination . '/' . $this->formatSourceLink($class, FALSE)));
				$generatedFiles[$file] = TRUE;

				$this->incrementProgressBar();
			}
		}
	}

	/**
	 * Returns a template instance with required helpers prepared.
	 *
	 * @return \Nette\Templates\FileTemplate
	 */
	private function createTemplate()
	{
		$template = new Nette\Templating\FileTemplate;
		$template->setCacheStorage(new Nette\Caching\Storages\MemoryStorage);

		$latte = new Nette\Latte\Engine;
		$latte->handler->macros['try'] = '<?php try { ?>';
		$latte->handler->macros['/try'] = '<?php } catch (\Exception $e) {} ?>';
		$template->registerFilter($latte);

		// common operations
		$template->registerHelperLoader('Nette\Templating\DefaultHelpers::loader');
		$template->registerHelper('ucfirst', 'ucfirst');
		$template->registerHelper('values', 'array_values');
		$template->registerHelper('map', function($arr, $callback) {
			return array_map(create_function('$value', $callback), $arr);
		});
		$template->registerHelper('replaceRE', 'Nette\StringUtils::replace');

		// PHP source highlight
		$fshl = new \fshlParser('HTML_UTF8');
		$template->registerHelper('highlightPHP', function($source) use ($fshl) {
			return $fshl->highlightString('PHP', (string) $source);
		});

		// links
		$template->registerHelper('packageLink', callback($this, 'formatPackageLink'));
		$template->registerHelper('namespaceLink', callback($this, 'formatNamespaceLink'));
		$template->registerHelper('classLink', callback($this, 'formatClassLink'));
		$template->registerHelper('methodLink', callback($this, 'formatMethodLink'));
		$template->registerHelper('propertyLink', callback($this, 'formatPropertyLink'));
		$template->registerHelper('constantLink', callback($this, 'formatConstantLink'));
		$template->registerHelper('sourceLink', callback($this, 'formatSourceLink'));

		// types
		$that = $this;
		$template->registerHelper('getTypes', function($element, $position = NULL) use ($that) {
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
				$annotation = $position === NULL ? $element->getAnnotation('return') : @$element->annotations['param'][$position];
			}

			$namespace = $element->getDeclaringClass()->getNamespaceName();
			$types = array();
			foreach (preg_replace('#\s.*#', '', (array) $annotation) as $s) {
				foreach (explode('|', $s) as $name) {
					$class = $that->resolveType($name, $namespace);
					$types[] = (object) array('name' => $class ?: $name, 'class' => $class);
				}
			}
			return $types;
		});
		$template->registerHelper('resolveType', callback($this, 'resolveType'));
		$template->registerHelper('getType', function($variable) {
			return is_object($variable) ? get_class($variable) : gettype($variable);
		});

		// docblock
		$texy = new \Texy;
		$texy->mergeLines = FALSE;
		$texy->allowedTags = \Texy::NONE;
		$texy->allowed['list/definition'] = FALSE;
		$texy->allowed['phrase/em-alt'] = FALSE;
		$texy->allowed['longwords'] = FALSE;
		$texy->registerBlockPattern( // highlight <code>, <pre>
			function($parser, $matches, $name) use ($fshl) {
				$content = $matches[1] === 'code' ? $fshl->highlightString('PHP', $matches[2]) : htmlSpecialChars($matches[2]);
				$content = $parser->getTexy()->protect($content, \Texy::CONTENT_BLOCK);
				return \TexyHtml::el('pre', $content);
			},
			'#<(code|pre)>(.+?)</\1>#s',
			'codeBlockSyntax'
		);

		// Documentation formatting
		$template->registerHelper('docline', function($text) use ($texy) {
			return $texy->processLine($text);
		});
		$template->registerHelper('docblock', function($text) use ($texy) {
			return $texy->process($text);
		});
		$template->registerHelper('doclabel', function($doc, $namespace) use ($template) {
			@list($names, $label) = preg_split('#\s+#', $doc, 2);
			$res = '';
			foreach (explode('|', $names) as $name) {
				$class = $template->resolveType($name, $namespace);
				$res .= $class !== NULL ? sprintf('<a href="%s">%s</a>', $template->classLink($class), $template->escapeHtml($class)) : $template->escapeHtml($name);
				$res .= '|';
			}
			return rtrim($res, '|') . ' ' . $template->escapeHtml($label);
		});

		// Docblock descriptions
		$template->registerHelper('longDescription', function($element, $shortIfNone = false) {
			$short = $element->getAnnotation(ReflectionAnnotation::SHORT_DESCRIPTION);
			$long = $element->getAnnotation(ReflectionAnnotation::LONG_DESCRIPTION);

			if ($long) {
				$short .= "\n\n" . $long;
			}

			return $short;
		});
		$template->registerHelper('shortDescription', function($element) {
			return $element->getAnnotation(ReflectionAnnotation::SHORT_DESCRIPTION);
		});

		// static files versioning
		$destination = $this->config['destination'];
		$template->registerHelper('staticFile', function($name, $line = null) use ($destination) {
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


		return $template;
	}

	/**
	 * Generates a link to a namespace summary file.
	 *
	 * @param  string|\Apigen\Reflection|IReflectionNamespace
	 * @return string
	 */
	public function formatNamespaceLink($class)
	{
		if (!isset($this->config['filenames']['namespace'])) {
			throw new Exception('Namespace output filename not defined.', Exception::INVALID_CONFIG);
		}

		$namespace = ($class instanceof ApiReflection) ? $class->getNamespaceName() : $class;
		return sprintf($this->config['filenames']['namespace'], $namespace ? preg_replace('#[^a-z0-9_]#i', '.', $namespace) : 'None');
	}

	/**
	 * Generates a link to a package summary file.
	 *
	 * @param  string|\Apigen\Reflection
	 * @return string
	 */
	public function formatPackageLink($class)
	{
		if (!isset($this->config['filenames']['package'])) {
			throw new Exception('Package output filename not defined.', Exception::INVALID_CONFIG);
		}

		$package = ($class instanceof ApiReflection) ? $class->getPackageName() : $class;
		return sprintf($this->config['filenames']['package'], $package ? preg_replace('#[^a-z0-9_]#i', '.', $package) : 'None');
	}

	/**
	 * Generates a link to class summary file.
	 *
	 * @param  string|\Apigen\Reflection $class
	 * @return string
	 */
	public function formatClassLink($class)
	{
		if (!isset($this->config['filenames']['class'])) {
			throw new Exception('Class output filename not defined.', Exception::INVALID_CONFIG);
		}

		if ($class instanceof ApiReflection) {
			$class = $class->getName();
		}

		return sprintf($this->config['filenames']['class'], preg_replace('#[^a-z0-9_]#i', '.', $class));
	}

	/**
	 * Generates a link to method in class summary file.
	 *
	 * @param IReflectionMethod $method
	 * @return string
	 */
	public function formatMethodLink(ReflectionMethod $method)
	{
		return $this->formatClassLink($method->getDeclaringClassName()) . '#_' . $method->getName();
	}

	/**
	 * Generates a link to property in class summary file.
	 *
	 * @param IReflectionProperty $property
	 * @return string
	 */
	public function formatPropertyLink(ReflectionProperty $property)
	{
		return $this->formatClassLink($property->getDeclaringClassName()) . '#$' . $property->getName();
	}

	/**
	 * Generates a link to constant in class summary file.
	 *
	 * @param IReflectionConstant $constant
	 * @return string
	 */
	public function formatConstantLink(ReflectionConstant $constant)
	{
		return $this->formatClassLink($constant->getDeclaringClassName()) . '#' . $constant->getName();
	}

	/**
	 * Generates a link to a class source code file.
	 *
	 * @param \Apigen\Reflection|IReflectionMethod|IReflectionProperty|IReflectionConstant $element
	 * @return string
	 */
	public function formatSourceLink($element, $withLine = TRUE)
	{
		if (!isset($this->config['filenames']['source'])) {
			throw new Exception('Source output filename not defined.', Exception::INVALID_CONFIG);
		}

		$class = $element instanceof ApiReflection ? $element : $element->getDeclaringClass();
		if ($class->isInternal()) {
			static $manual = 'http://php.net/manual';
			static $reservedClasses = array('stdClass', 'Closure', 'Directory');

			if (in_array($class->getName(), $reservedClasses)) {
				return $manual . '/reserved.classes.php';
			}

			$className = strtolower($class->getName());
			$classLink = sprintf('%s/class.%s.php', $manual, $className);
			$elementName = strtolower(strtr(ltrim($element->getName(), '_'), '_', '-'));

			if ($element instanceof ApiReflection) {
				return $classLink;
			} elseif ($element instanceof ReflectionMethod) {
				return sprintf('%s/%s.%s.php', $manual, $className, $elementName);
			} elseif ($element instanceof ReflectionProperty) {
				return sprintf('%s#%s.props.%s', $classLink, $className, $elementName);
			} elseif ($element instanceof ReflectionConstant) {
				return sprintf('%s#%s.constants.%s', $classLink, $className, $elementName);
			}
		} elseif ($class->isTokenized()) {
			$file = str_replace('\\', '/', $class->getName());

			$line = null;
			if ($withLine) {
				$line = $element->getStartLine();
				if ($doc = $element->getDocComment()) {
					$line -= substr_count($doc, "\n") + 1;
				}
			}

			return sprintf($this->config['filenames']['source'], preg_replace('#[^a-z0-9_]#i', '.', $file)) . (isset($line) ? "#$line" : '');
		}
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
		return isset($this->classes["$namespace\\$type"]) ? "$namespace\\$type" : (isset($this->classes[$type]) ? $type : NULL);
	}

	/**
	 * Prepares the progressbar.
	 *
	 * @param $maximum Maximum progressbar value
	 */
	private function prepareProgressBar($maximum = 1)
	{
		$this->progressBar = new \Console_ProgressBar(
			'[%bar%] %percent%',
			'=>',
			' ',
			80,
			$maximum
		);
	}

	/**
	 * Increments the progressbar by one.
	 */
	protected function incrementProgressBar()
	{
		if ($this->config['progressbar']) {
			$this->progressBar->update($this->progressBar->getProgress() + 1);
		}
	}

	/**
	 * Ensures a directory is created.
	 *
	 * @param string Directory path
	 * @return string
	 */
	public static function forceDir($path)
	{
		@mkdir(dirname($path), 0755, TRUE);
		return $path;
	}
}
