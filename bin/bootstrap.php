<?php

declare(strict_types=1);

define('APIGEN_CLI_ROOT_DIR', realpath(__DIR__.'/../'));

// Cloned, downloaded or installed via `composer create-project`
define('APIGEN_CLI_LOCAL_CONTEXT', 'local');
// Installed via `composer [global] require`
define('APIGEN_CLI_DIST_CONTEXT', 'distributed'); // Installed via composer require


$local_autoloader = realpath(APIGEN_CLI_ROOT_DIR.'/vendor/autoload.php');
$dist_autoloader  = realpath(APIGEN_CLI_ROOT_DIR.'/../../autoload.php');

if ($local_autoloader) {
    $cli_autoloader = $local_autoloader;
    define('APIGEN_CLI_CONTEXT', APIGEN_CLI_LOCAL_CONTEXT);
} elseif ($dist_autoloader) {
    $cli_autoloader = $dist_autoloader;
    define('APIGEN_CLI_CONTEXT', APIGEN_CLI_DIST_CONTEXT);
} else {
    $cli_autoloader = null;
    define('APIGEN_CLI_CONTEXT', null);

    throw new RuntimeException('Error! Unable to find ApiGen autoloader.');
}

require_once $cli_autoloader;
