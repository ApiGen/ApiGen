<?php declare(strict_types=1);

namespace ApiGen\Annotation;

final class AnnotationList
{
    /**
     * @var string
     */
    public const DEPRECATED = 'deprecated';

    /**
     * @var string
     */
    public const PARAM = 'param';

    /**
     * @var string
     */
    public const VAR_ = 'var';

    /**
     * @var string
     */
    public const THROWS = 'throws';

    /**
     * @var string
     */
    public const RETURN_ = 'return';

    /**
     * @var string
     */
    public const SEE = 'see';

    /**
     * @var string
     */
    public const LINK = 'link';

    /**
     * @var string
     */
    public const USES = 'uses';

    /**
     * @var string
     */
    public const COVERS = 'covers';

    /**
     * @var string
     */
    public const EMPTY_LINE = PHP_EOL . PHP_EOL;
}
