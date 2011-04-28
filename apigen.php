<?php

/**
 * ApiGen - API Generator.
 * http://apigen.org
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011 Ondřej Nešpor (http://andrewsville.cz)
 * Copyright (c) 2011 Jaroslav Hanslík (http://kukulich.cz)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

use NetteX\Diagnostics\Debugger;


require __DIR__ . '/libs/NetteX/nette.min.php';
require __DIR__ . '/libs/fshl/fshl.php';
require __DIR__ . '/libs/Console/ProgressBar.php';
require __DIR__ . '/libs/texy/texy.min.php';
require __DIR__ . '/libs/TokenReflection/tokenreflection.min.php';
require __DIR__ . '/libs/Apigen/Exception.php';
require __DIR__ . '/libs/Apigen/Reflection.php';
require __DIR__ . '/libs/Apigen/Backend.php';
require __DIR__ . '/libs/Apigen/Generator.php';

try {

	$name = sprintf('ApiGen %s', Apigen\Generator::VERSION);
	echo $name . "\n";
	echo str_repeat('-', strlen($name)) . "\n";

	Debugger::enable();
	Debugger::timer();

	$options = getopt('', array(
		'config:',
		'source:',
		'destination:',
		'title:',
		'base-url:',
		'google-cse:',
		'template:',
		'template-dir:',
		'access-levels:',
		'wipeout:',
		'progressbar:'
	));

	if (isset($options['config'])) {
		$config = NetteX\Utils\Neon::decode(file_get_contents($options['config']));
	} elseif (isset($options['source'], $options['destination'])) {
		$config = array();
		foreach ($options as $key => $value) {
			$key = preg_replace_callback('#-([a-z])#', function($matches) {
				return ucfirst($matches[1]);
			}, $key);

			if ('off' === strtolower($value)) {
				$value = false;
			} elseif ('on' === strtolower($value)) {
				$value = true;
			}
			if ('accessLevels' === $key) {
				$value = explode(',', $value);
			}

			$config[$key] = $value;
		}
	} else {
		throw new Exception('Invalid configuration');
	}

	// Merge default configuration
	$defaultConfig = array(
		'title' => '',
		'baseUrl' => '',
		'googleCse' => '',
		'template' => '',
		'templateDir' => '',
		'accessLevels' => array('public', 'protected'),
		'wipeout' => true,
		'progressbar' => true
	);
	$config = array_merge($defaultConfig, $config);

	// Fix configuration
	if (empty($config['template'])) {
		$config['template'] = 'default';
	}
	if (empty($config['templateDir'])) {
		$config['templateDir'] = __DIR__ . DIRECTORY_SEPARATOR  . 'templates';
	}
	foreach (array('source', 'destination', 'templateDir') as $key) {
		if (is_dir($config[$key])) {
			$config[$key] = realpath($config[$key]);
		}
	}
	$config['accessLevels'] = array_filter($config['accessLevels'], function($item) {
		return in_array($item, array('public', 'protected', 'private'));
	});

	// Check configuration
	if (!is_dir($config['templateDir'])) {
		throw new Exception(sprintf('Template directory %s doesn\'t exist', $config['templateDir']));
	}
	$templatePath = $config['templateDir'] . DIRECTORY_SEPARATOR . $config['template'];
	if (!is_dir($templatePath)) {
		throw new Exception('Template doesn\'t exist');
	}
	$templateConfigPath = $templatePath . DIRECTORY_SEPARATOR . 'config.neon';
	if (!is_file($templateConfigPath)) {
		throw new Exception('Template config doesn\'t exist');
	}
	if (empty($config['source'])) {
		throw new Exception('Source directory is not set');
	} elseif (!is_dir($config['source'])) {
		throw new Exception(sprintf('Source directory %s doesn\'t exist', $config['source']));
	}
	if (empty($config['destination'])) {
		throw new Exception('Destination directory is not set');
	}
	if (empty($config['accessLevels'])) {
		throw new Exception('No supported access level given');
	}

	// Merge template config
	$config = array_merge($config, NetteX\Utils\Neon::decode(file_get_contents($templateConfigPath)));

	// Start
	$generator = new Apigen\Generator($config);

	// Scan
	echo "Scanning directory $config[source]\n";
	list($count, $countInternal) = $generator->parse();
	echo "Found $count classes and $countInternal internal classes\n";

	// Generating
	echo "Searching template in $config[templateDir]\n";
	echo "Using template $config[template]\n";
	if ($config['wipeout'] && is_dir($config['destination'])) {
		echo "Wiping out destination directory\n";
		if (!$generator->wipeOutDestination()) {
			throw new Exception('Cannot wipe out destination directory');
		}
	}

	echo "Generating documentation to directory $config[destination]\n";
	$generator->generate();

	// End
	echo "Done. Total time: " . (int) Debugger::timer() . " seconds, used: " . round(memory_get_peak_usage(true) / 1024 / 1024) . " MB RAM\n";

} catch (Exception $e) {
	echo "\n" . $e->getMessage() . "\n\n";
?>
Usage:
	apigen --config=<path>
	apigen --source=<path> --destination=<path> [options]

Options:
	--config        <path>  Config file
	--source        <path>  Name of a source directory to parse
	--destination   <path>  Directory where to save the generated documentation
	--title         <value> Title of generated documentation
	--base-url      <value> Documentation base URI
	--google-cse    <value> Google Custom Search ID
	--template      <value> Documentation template name
	--template-dir  <path>  Directory with templates
	--access-levels <list>  Generate documetation for methods and properties with given access level, default public,protected
	--wipeout       On|Off  Wipe out the destination directory first, default On
	--progressbar   On|Off  Display progressbars, default On

Only source and destination directories are required - either set explicitly or using a config file.
<?php
	die(1);
}