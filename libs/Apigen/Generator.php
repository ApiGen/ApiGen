<?php

/**
 * API Generator.
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 *
 * This source file is subject to the "Nette license", and/or
 * GPL license. For more information please see http://nette.org
 */

namespace Apigen;

use NetteX;



/**
 * Generates a HTML API documentation based on model.
 * @author     David Grudl
 */
class Generator extends NetteX\Object
{
	/** @var Model */
	private $model;



	public function __construct(Model $model)
	{
		$this->model = $model;
	}



	/**
	 * Generates API documentation.
	 * @param  string  output directory
	 * @param  array
	 * @void
	 */
	public function generate($output, $config)
	{
		if (!is_dir($output)) {
			throw new \Exception("Directory $output doesn't exist.");
		}

		// copy resources
		foreach ($config['resources'] as $source => $dest) {
			foreach ($iterator = NetteX\Finder::findFiles('*')->from($source)->getIterator() as $foo) {
				copy($iterator->getPathName(), self::forceDir("$output/$dest/" . $iterator->getSubPathName()));
			}
		}

		// categorize by namespaces
		$namespaces = array();
		$allClasses = array();
		foreach ($this->model->getClasses() as $class) {
			$namespaces[$class->isInternal() ? 'PHP' : $class->getNamespaceName()][$class->getShortName()] = $class;
			$allClasses[$class->getShortName()] = $class;
		}
		uksort($namespaces, 'strcasecmp');
		uksort($allClasses, 'strcasecmp');

		$template = $this->createTemplate();
		$template->fileRoot = $this->model->getDirectory();
		foreach ($config['variables'] as $key => $value) {
			$template->$key = $value;
		}

		// generate summary files
		$template->namespaces = array_keys($namespaces);
		$template->classes = $allClasses;
		foreach ($config['templates']['common'] as $dest => $source) {
			$template->setFile($source)->save(self::forceDir("$output/$dest"));
		}

		$generatedFiles = array();
		$fshl = new \fshlParser('HTML_UTF8', P_TAB_INDENT | P_LINE_COUNTER);
		foreach ($namespaces as $namespace => $classes) {
			// generate namespace summary
			uksort($classes, 'strcasecmp');
			$template->namespace = $namespace;
			$template->classes = $classes;
			$template->setFile($config['templates']['namespace'])->save(self::forceDir($output . '/' . $this->formatNamespaceLink($namespace)));

			// generate class & interface files
			foreach ($classes as $class) {
				$template->tree = array($class);
				while ($parent = $template->tree[0]->getParentClass()) {
					array_unshift($template->tree, $parent);
				}
				$template->subClasses = $this->model->getDirectSubClasses($class);
				uksort($template->subClasses, 'strcasecmp');
				$template->implementers = $this->model->getDirectImplementers($class);
				uksort($template->implementers, 'strcasecmp');
				$template->class = $class;
				$template->setFile($config['templates']['class'])->save(self::forceDir($output . '/' . $this->formatClassLink($class)));

				// generate source codes
				if (!$class->isInternal() && !isset($generatedFiles[$class->getFileName()])) {
					$file = $class->getFileName();
					$template->source = $fshl->highlightString('PHP', file_get_contents($file));
					$template->fileName = substr($file, strlen($this->model->getDirectory()) + 1);
					$template->setFile($config['templates']['source'])->save(self::forceDir($output . '/' . $this->formatSourceLink($class, FALSE)));
					$generatedFiles[$file] = TRUE;
				}
			}
		}
	}



	/** @return NetteX\Templates\FileTemplate */
	private function createTemplate()
	{
		$template = new NetteX\Templates\FileTemplate;
		$template->setCacheStorage(new NetteX\Caching\MemoryStorage);

		$latte = new NetteX\Templates\LatteFilter;
		$latte->handler->macros['try'] = '<?php try { ?>';
		$latte->handler->macros['/try'] = '<?php } catch (\Exception $e) {} ?>';
		$template->registerFilter($latte);

		// common operations
		$template->registerHelperLoader('NetteX\Templates\TemplateHelpers::loader');
		$template->registerHelper('ucfirst', 'ucfirst');
		$template->registerHelper('values', 'array_values');
		$template->registerHelper('map', function($arr, $callback) {
			return array_map(create_function('$value', $callback), $arr);
		});
		$template->registerHelper('replaceRE', 'NetteX\String::replace');
		$template->registerHelper('replaceNS', function($name, $namespace) { // remove current namespace
			return (strpos($name, $namespace . '\\') === 0 && strpos($name, '\\', strlen($namespace) + 1) === FALSE)
				? substr($name, strlen($namespace) + 1) : $name;
		});
		$fshl = new \fshlParser('HTML_UTF8');
		$template->registerHelper('dump', function($val) use ($fshl) {
			return $fshl->highlightString('PHP', var_export($val, TRUE));
		});

		// links
		$template->registerHelper('namespaceLink', callback($this, 'formatNamespaceLink'));
		$template->registerHelper('classLink', callback($this, 'formatClassLink'));
		$template->registerHelper('sourceLink', callback($this, 'formatSourceLink'));

		// docblock
		$texy = new \TexyX;
		$texy->allowed['list/definition'] = FALSE;
		$texy->allowed['phrase/em-alt'] = FALSE;
		$texy->registerBlockPattern( // highlight <code>, <pre>
			function($parser, $matches, $name) use ($fshl) {
				$content = $matches[1] === 'code' ? $fshl->highlightString('PHP', $matches[2]) : htmlSpecialChars($matches[2]);
				$content = $parser->getTexy()->protect($content, \TexyX::CONTENT_BLOCK);
				return \TexyXHtml::el('pre', $content);
			},
			'#^<(code|pre)>\n(.+?)\n</\1>$#ms', // block patterns must be multiline and line-anchored
			'codeBlockSyntax'
		);

		$template->registerHelper('docline', function($doc, $line = TRUE) use ($texy) {
			$doc = Model::extractDocBlock($doc);
			$doc = preg_replace('#\n.*#s', '', $doc); // leave only first line
			return $line ? $texy->processLine($doc) : $texy->process($doc);
		});

		$template->registerHelper('docblock', function($doc) use ($texy) {
			return $texy->process(Model::extractDocBlock($doc));
		});

		// types
		$model = $this->model;
		$template->registerHelper('getTypes', function($element, $position = NULL) use ($model) {
			$namespace = $element->getDeclaringClass()->getNamespaceName();
			$s = $position === NULL ? $element->getAnnotation($element instanceof \ReflectionProperty ? 'var' : 'return')
				: @$element->annotations['param'][$position];
			if (is_object($s)) {
				$s = get_class($s); // TODO
			}
			$s = preg_replace('#\s.*#', '', $s);
			$res = array();
			foreach (explode('|', $s) as $name) {
				$res[] = (object) array('name' => $name, 'class' => $model->resolveType($name, $namespace));
			}
			return $res;
		});
		$template->registerHelper('resolveType', callback($model, 'resolveType'));

		return $template;
	}



	/**
	 * Generates link to namespace summary file.
	 * @param  string|ReflectionClass
	 * @return string
	 */
	public function formatNamespaceLink($class)
	{
		$namescape = $class instanceof \ReflectionClass ? $class->getNamespaceName() : $class;
		return 'namespace-' . ($namescape ? preg_replace('#[^a-z0-9_]#i', '.', $namescape) : 'none') . '.html';
	}



	/**
	 * Generates link to class summary file.
	 * @param  string|ReflectionClass|ReflectionMethod|ReflectionProperty
	 * @return string
	 */
	public function formatClassLink($element)
	{
		$id = '';
		if (is_string($element)) {
			$class = $element;
		} elseif ($element instanceof \ReflectionClass) {
			$class = $element->getName();
		} else {
			$class = $element->getDeclaringClass()->getName();
			if ($element instanceof \ReflectionProperty) {
				$id = '#$' . $element->getName();
			} elseif ($element instanceof \ReflectionMethod) {
				$id = '#_' . $element->getName();
			}
		}
		return preg_replace('#[^a-z0-9_]#i', '.', $class) . '.html' . $id;
	}



	/**
	 * Generates link to class source code file.
	 * @param  ReflectionClass|ReflectionMethod
	 * @return string
	 */
	public function formatSourceLink($element, $withLine = TRUE)
	{
		$class = $element instanceof \ReflectionClass ? $element : $element->getDeclaringClass();
		if ($class->isInternal()) {
			if ($element instanceof \ReflectionClass) {
				return strtolower('http://php.net/manual/class.' . $class->getName() . '.php');
			} else {
				return strtolower('http://php.net/manual/' . $class->getName() . '.' . strtr(ltrim($element->getName(), '_'), '_', '-') . '.php');
			}
		} else {
			$file = substr($element->getFileName(), strlen($this->model->getDirectory()) + 1);
			$line = $withLine ? ($element->getStartLine() - substr_count($element->getDocComment(), "\n") - 1) : NULL;
			return 'source-' . preg_replace('#[^a-z0-9_]#i', '.', $file) . '.html' . (isset($line) ? "#$line" : '');
		}
	}



	/**
	 * Ensures directory is created.
	 * @param  string
	 * @return string
	 */
	public static function forceDir($path)
	{
		@mkdir(dirname($path), 0755, TRUE);
		return $path;
	}

}
