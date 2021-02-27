<?php declare(strict_types = 1);

require __DIR__ . '/../../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$in = new React\Stream\ReadableResourceStream(STDIN, $loop);
$out = new React\Stream\WritableResourceStream(STDOUT, $loop);
$messageStream = new ApiGenX\TaskExecutor\MessageStream($in, $out);
$env = new ApiGenX\TaskExecutor\DefaultTaskEnvironment();

$messageStream->on('data', function (ApiGenX\TaskExecutor\Task $task) use ($loop, $env, $messageStream) {
	$loop->futureTick(function () use ($task, $env, $messageStream) {
		$messageStream->write($task->run($env));
	});
});

$loop->run();
