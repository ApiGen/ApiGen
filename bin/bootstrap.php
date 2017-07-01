<?php

declare(strict_types=1);

/**
 * @var string ApiGen root directory.
 * */
define('APIGEN_CLI_ROOT_DIR', realpath(__DIR__.'/../'));

$standalone = null;

// Check for autoloader in a local install (not installed via `composer [global] require`)
$autoloader = realpath(APIGEN_CLI_ROOT_DIR.'/vendor/autoload.php');

if ($autoloader) {
    // ApiGen is installed as clone, direct download or `composer create-project`.
    // So, ApiGen is not a composer dependency.
    $standalone = true;
} else {
    // Installed via `composer [global] require`.
    $autoloader = realpath(APIGEN_CLI_ROOT_DIR.'/../../autoload.php');
    
    if ($autoloader) {
        $standalone = false;
    }
}

/**
 * @var bool|null Indicates if runing ApiGen was installed as standalone. 
 *   If is set to `null`, no autoloader was found.
 * */
define('APIGEN_IS_STANDALONE', $standalone);

if (!$autoloader) {
    throw new RuntimeException(
        'Error! ApiGen was unable to find its autoloader. '.
        'Did you forget to run "composer update"?'
    );
}

require_once $autoloader;
