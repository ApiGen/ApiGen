<?php declare(strict_types=1);

namespace ApiGen\Application\Command;

use ApiGen\ModularConfiguration\Option\DestinationOption;
use ApiGen\ModularConfiguration\Option\SourceOption;
use Symfony\Component\Console\Input\InputInterface;

final class RunCommand
{
    /**
     * @var string[]
     */
    private $source = [];

    /**
     * @var string
     */
    private $destination;

    /**
     * @param string[] $source
     */
    private function __construct(array $source, string $destination)
    {
        $this->source = $source;
        $this->destination = $destination;
    }

    public static function createFromInput(InputInterface $input): self
    {
        return new self(
            $input->getArgument(SourceOption::NAME),
            $input->getOption(DestinationOption::NAME)
        );
    }

    /**
     * @return string[]
     */
    public function getSource(): array
    {
        return $this->source;
    }

    public function getDestination(): string
    {
        return $this->destination;
    }
}
