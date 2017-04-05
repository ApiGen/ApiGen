<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Elements;

interface ElementsInterface
{
    /**
     * @var string
     */
    public const CLASSES = 'classes';

    /**
     * @var string
     */
    public const EXCEPTIONS = 'exceptions';

    /**
     * @var string
     */
    public const FUNCTIONS = 'functions';

    /**
     * @var string
     */
    public const INTERFACES = 'interfaces';

    /**
     * @var string
     */
    public const TRAITS = 'traits';

    /**
     * @var string
     */
    public const PROPERTIES = 'properties';

    /**
     * @var string
     */
    public const METHODS = 'methods';

    /**
     * @return string[]
     */
    public function getClassTypeList(): array;

    /**
     * @return string[]
     */
    public function getAll(): array;

    /**
     * @return mixed[]
     */
    public function getEmptyList(): array;
}
