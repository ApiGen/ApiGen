<?php declare(strict_types=1);

namespace ApiGen\Contracts\Configuration;

interface ConfigurationInterface
{
    public function resolveOptions(array $options): array;


    /**
     * @return mixed|NULL
     */
    public function getOption(string $name);


    public function getOptions(): array;


    public function setOptions(array $options): void;


    /**
     * Get property/method visibility level (public, protected or private, in binary code).
     */
    public function getVisibilityLevel(): int;


    /**
     * Return name of main library
     */
    public function getMain(): string;


    /**
     * Are elements marked as "@internal" documented.
     */
    public function isInternalDocumented(): bool;


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


    public function getGoogleCseId(): string;


    public function shouldGenerateSourceCode(): bool;


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
