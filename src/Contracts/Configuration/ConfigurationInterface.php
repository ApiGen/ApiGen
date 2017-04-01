<?php declare(strict_types=1);

namespace ApiGen\Contracts\Configuration;

interface ConfigurationInterface
{
    /**
     * @param mixed[] $options
     * @return mixed[]
     */
    public function resolveOptions(array $options): array;

    /**
     * @return mixed|null
     */
    public function getOption(string $name);

    /**
     * @return mixed[]
     */
    public function getOptions(): array;

    /**
     * @param mixed[] $options
     */
    public function setOptions(array $options): void;

    /**
     * Get property/method visibility level (public, protected or private, in binary code).
     */
    public function getVisibilityLevels(): int;

    /**
     * List of annotations.
     *
     * @return string[]
     */
    public function getAnnotationGroups(): array;

    public function getDestination(): string;

    /**
     * Get title of the project.
     */
    public function getTitle(): string;

    /**
     * Base url of application.
     */
    public function getBaseUrl(): string;

    public function getGoogleAnalytics(): string;

    /**
     * @return string[]
     */
    public function getSource(): array;

    /**
     * Exclude masks for files/directories.
     *
     * @return string[]
     */
    public function getExclude(): array;

    /**
     * File extensions to be taken in account.
     *
     * @return string[]
     */
    public function getExtensions(): array;
}
