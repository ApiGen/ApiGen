<?php declare(strict_types=1);

$rootDir = realpath(__DIR__ . '/../');

// Autoloader for standalone install (installed via `composer create-project` or cloned locally)
$autoloader = realpath($rootDir . '/vendor/autoload.php');

if (! $autoloader) {
    // Installed via `composer [global] require`.
    $autoloader = realpath($rootDir . '/../../autoload.php');
}

if (! $autoloader) {
    throw new RuntimeException(
        'ApiGen was unable to find its autoloader. ' .
        'Did you forget to run "composer update"?'
    );
}

require_once $autoloader;
