<?php declare(strict_types=1);

namespace ApiGen\Contracts\Templating;

interface TemplateRendererInterface
{
    /**
     * @param string $templateFile
     * @param string $destinationFile
     * @param mixed[] $parameters
     */
    public function renderToFile(string $templateFile, string $destinationFile, array $parameters = []): void;
}
