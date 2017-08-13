<?php declare(strict_types=1);

namespace ApiGen\Reflection\DocBlock\Tags;

use Nette\Utils\Validators;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;
use phpDocumentor\Reflection\DocBlock\Tags\Factory\StaticMethod;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\Types\Context as TypeContext;
use Webmozart\Assert\Assert;

final class See extends BaseTag implements StaticMethod
{
    /**
     * @var string
     */
    protected $name = 'see';

    /**
     * @var Fqsen
     */
    protected $refers;

    /**
     * @var string
     */
    private $link;

    public function __construct(?Fqsen $refers = null, ?string $link = null, ?Description $description = null)
    {
        $this->refers = $refers;
        $this->link = $link;
        $this->description = $description;
    }

    public function __toString(): string
    {
        return ($this->refers ?: $this->link) . ($this->description ? ' ' . $this->description->render() : '');
    }

    /**
     * @param string $body
     */
    public static function create(
        $body,
        FqsenResolver $resolver = null,
        DescriptionFactory $descriptionFactory = null,
        TypeContext $context = null
    ): self {
        Assert::string($body);
        Assert::allNotNull([$resolver, $descriptionFactory]);

        $parts = preg_split('/\s+/Su', $body, 2);

        if (! Validators::isUrl($parts[0])) {
            $fqsen = $resolver->resolve($parts[0], $context);
            $link = null;
        } else {
            $link = $parts[0];
            $fqsen = null;
        }

        $description = isset($parts[1]) ? $descriptionFactory->create($parts[1], $context) : null;

        return new static($fqsen, $link, $description);
    }

    public function getReference(): ?Fqsen
    {
        return $this->refers;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }
}
