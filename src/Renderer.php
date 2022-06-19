<?php declare(strict_types = 1);

namespace ApiGenX;

use ApiGenX\Index\Index;
use Symfony\Component\Console\Helper\ProgressBar;


interface Renderer
{
	public function render(ProgressBar $progressBar, Index $index): void;
}
