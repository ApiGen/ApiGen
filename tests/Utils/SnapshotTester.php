<?php declare(strict_types = 1);

namespace ApiGenTests\Utils;

use Nette\Utils\FileSystem;
use Tester\Assert;

use function getenv;
use function is_file;


class SnapshotTester
{
	public static function assertSnapshotSame(string $snapshotPath, string $actual): void
	{
		if (!is_file($snapshotPath) && getenv('CI') === false) {
			FileSystem::write($snapshotPath, $actual);

		} else {
			$expected = FileSystem::read($snapshotPath);
			Assert::same($expected, $actual);
		}
	}
}
