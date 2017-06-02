<?php declare(strict_types=1);

include __DIR__ . '/../vendor/autoload.php';

define('TEMP_DIR', sys_get_temp_dir() . '/_apigen_cache');
ApiGen\Utils\FileSystem::ensureDirectoryExists(TEMP_DIR);

register_shutdown_function(function () {
    Nette\Utils\FileSystem::delete(TEMP_DIR);
});
