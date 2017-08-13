<?php declare(strict_types=1);

$loader = new Composer\Autoload\ClassLoader;

// Files of the project that API is being generated for
$loader->add('MyVendor', __DIR__ . '/../src');

// Project's dependencies
$loader->addClassMap([
    'DifferentVendor\\DifferentClass' => __DIR__ . '/DifferentVendor/DifferentClass.php',
]);

$loader->register(true);

return $loader;
