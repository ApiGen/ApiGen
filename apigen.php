<?php

/**
 * ApiGen - API Generator.
 * http://apigen.org
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

use NetteX\Diagnostics\Debugger;


require __DIR__ . '/libs/NetteX/nettex.min.php';
require __DIR__ . '/libs/fshl/fshl.php';
require __DIR__ . '/libs/TexyX/texyx.min.php';
require __DIR__ . '/libs/Apigen/CustomClassReflection.php';
require __DIR__ . '/libs/Apigen/Model.php';
require __DIR__ . '/libs/Apigen/Generator.php';

echo '
ApiGen version 1.0
------------------
';

$options = getopt('s:d:c:t:l:');

if (!isset($options['s'], $options['d'])) { ?>
Usage:
	apigen [options]

Options:
	-s <path>  Name of a source directory to parse. Required.
	-d <path>  Folder where to save the generated documentation. Required.
	-c <path>  Output config file.
	-l <path>  Directory with additional libraries.
	-t ...     Title of generated documentation.

<?php
	die();
}



date_default_timezone_set('Europe/Prague');
Debugger::enable();
Debugger::timer();

if (isset($options['l'])) {
  $robot = new NetteX\Loaders\RobotLoader;
  $robot->setCacheStorage(new NetteX\Caching\Storages\MemoryStorage);
  $robot->addDirectory($options['l']);
  $robot->register();
}

echo "Scanning folder $options[s]\n";
$model = new Apigen\Model;
$model->parse($options['s']);
$count = count($model->getClasses());

$model->expand();
$countD = count($model->getClasses()) - $count;

echo "Found $count classes and $countD system classes\n";



$configPath = isset($options['c']) ? $options['c'] : __DIR__ . '/config.neon';
$config = str_replace('%dir%', dirname($configPath), file_get_contents($configPath));
$config = NetteX\Utils\Neon::decode($config);
if (isset($options['t'])) {
	$config['variables']['title'] = $options['t'];
}


echo "Generating documentation to folder $options[d]\n";
@mkdir($options['d']);
foreach (NetteX\Utils\Finder::find('*')->from($options['d'])->childFirst() as $item) {
	if ($item->isDir()) {
		rmdir($item);
	} elseif ($item->isFile()) {
		unlink($item);
	}
}
$generator = new Apigen\Generator($model);
$generator->generate($options['d'], $config);



echo 'Done. Total time: ' . (int) Debugger::timer() . " seconds\n";
