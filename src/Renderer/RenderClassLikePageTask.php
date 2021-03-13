<?php declare(strict_types=1);

namespace ApiGenX\Renderer;

use ApiGenX\TaskExecutor\Task;
use ApiGenX\TaskExecutor\TaskEnvironment;
use ApiGenX\Templates\ClassicX\ClassLikeTemplate;
use ApiGenX\Templates\ClassicX\GlobalParameters;
use Nette\Utils\FileSystem;


final class RenderClassLikePageTask implements Task
{
	public function __construct(
		private string $classLikeLower,
		private string $output,
	) {
	}

	public function run(TaskEnvironment $env)
	{
		/** @var \ApiGenX\Index\Index $index */
		$index = $env['index'];

		/** @var \Latte\Engine $index */
		$latte = $env['latte'];

		$info = $index->classLike[$this->classLikeLower];

		$template = new ClassLikeTemplate(
			global: new GlobalParameters(
				index: $env['index'],
				title: '...',
				activePage: 'classLike',
				activeNamespace: $index->namespace[$info->name->namespaceLower],
				activeClassLike: $info,
			),
			classLike: $info,
		);

		FileSystem::write($this->output, $latte->renderToString(__DIR__ . '/../Templates/ClassicX/ClassLike.latte', $template));
		return [];
	}
}
