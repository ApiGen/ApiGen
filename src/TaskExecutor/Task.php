<?php declare(strict_types = 1);

namespace ApiGenX\TaskExecutor;


interface Task
{
	public function run(TaskEnvironment $env);
}
