<?php declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';


$directoriesSourceLocator = new \Roave\BetterReflection\SourceLocator\Type\DirectoriesSourceLocator([__DIR__ . '/api-source']);
$classReflector = new \Roave\BetterReflection\Reflector\ClassReflector($directoriesSourceLocator);

$ref = \Roave\BetterReflection\Reflection\ReflectionClass::createFromName(\Nette\DI\CompilerExtension::class);
dump($ref);
