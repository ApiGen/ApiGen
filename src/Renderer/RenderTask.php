<?php declare(strict_types = 1);

namespace ApiGenX\Renderer;

use ApiGenX\TaskExecutor\Task;
use ApiGenX\TaskExecutor\TaskEnvironment;
use Latte;
use League;
use Nette\Utils\FileSystem;


final class RenderTask implements Task
{
	public function __construct(
		private string $baseDir,
		private string $template,
		private object $parameters,
		private string $output,
	)
	{
	}

	public function run(TaskEnvironment $env)
	{
//		$env['latte'] ??= $this->createLatte();
//
//		$this->parameters->global->index = $env['index'];
//		FileSystem::write($this->output, $env['latte']->renderToString($this->template, $this->parameters));
		return [];
	}
}
