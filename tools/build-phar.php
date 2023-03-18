<?php declare(strict_types = 1);

require __DIR__ . '/../vendor/autoload.php';

$rootDir = __DIR__ . '/..';

$include = [
	"$rootDir/bin/**/*",
	"$rootDir/src/**/*",
	"$rootDir/vendor/**/*",
	"$rootDir/apigen.neon",
	"$rootDir/composer.json",
	"$rootDir/composer.lock",
	"$rootDir/LICENSE",
	"$rootDir/README.md",
];

$files = Nette\Utils\Finder::findFiles(...$include);

$stub = <<<'STUB'
	#!/usr/bin/env php
	<?php declare(strict_types = 1);

	if (!class_exists(Phar::class)) {
		echo "Missing phar extension which is required to run ApiGen.\n";
		exit(1);
	}

	Phar::mapPhar('apigen.phar');
	require 'phar://apigen.phar/bin/apigen';
	__HALT_COMPILER();
	STUB;

/**
 * Based on https://github.com/Seldaek/phar-utils by Jordi Boggiano licensed under MIT license.
 */
$setPharTimestamps = function (string $content, int $timestamp = 0): string {
	if (!preg_match('#__HALT_COMPILER\(\);(?: +\?>)?\r?\n#', $content, $match, PREG_OFFSET_CAPTURE)) {
		throw new \RuntimeException('Could not detect the stub\'s end in the phar');
	}

	$pos = $match[0][1] + strlen($match[0][0]);
	$end = $pos + 4 + unpack('V', $content, $pos)[1]; // read manifest length
	$pos += 4 + 4 + 2 + 4; // skip manifest length, number of files, API version and phar flags
	$pos += 4 + unpack('V', $content, $pos)[1]; // skip phar alias
	$pos += 4 + unpack('V', $content, $pos)[1]; // skip phar metadata
	$timestampBytes = pack('V', $timestamp);
	$dataLength = 0;

	while ($pos < $end) {
		$pos += 4 + unpack('V', $content, $pos)[1]; // skip file name length
		$pos += 4; // skip uncompressed file size

		for ($i = 0; $i < strlen($timestampBytes); $i++) {
			$content[$pos++] = $timestampBytes[$i];
		}

		$dataLength += unpack('V', $content, $pos)[1]; // read compressed file size
		$pos += 4 + 4 + 4; // skip compressed file size, crc32, file flags
		$pos += 4 + unpack('V', $content, $pos)[1]; // skip file metadata
	}

	$pos += $dataLength; // skip data
	$algorithms = [Phar::MD5 => 'md5', Phar::SHA1 => 'sha1', Phar::SHA256 => 'sha256', Phar::SHA512 => 'sha512'];
	$algorithm = $algorithms[unpack('V', $content, strlen($content) - 8)[1]];
	$signature = hash($algorithm, substr($content, 0, $pos), binary: true);

	for ($i = 0; $i < strlen($signature); $i++) {
		$content[$pos++] = $signature[$i];
	}

	return $content;
};

$phar = new Phar(__DIR__ . '/apigen.phar');
$phar->buildFromIterator($files, $rootDir);
$phar->setStub($stub);

Nette\Utils\FileSystem::write($phar->getPath(), $setPharTimestamps(Nette\Utils\FileSystem::read($phar->getPath())));
