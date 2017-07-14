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
    private $url;

    public function __construct(?Fqsen $refers = null, ?string $url = null, ?Description $description = null)
    {
        $this->refers = $refers;
        $this->url = $url;
        $this->description = $description;
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

        if (! Validators::isUri($parts[0])) {
            $fqsen = $resolver->resolve($parts[0], $context);
            $url = null;
        } else {
            $url = $parts[0];
            $fqsen = null;
        }

        $description = isset($parts[1]) ? $descriptionFactory->create($parts[1], $context) : null;

        return new static($fqsen, $url, $description);
    }

    public function getReference(): ?Fqsen
    {
        return $this->refers;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function __toString(): string
    {
        return ($this->refers ?: $this->url) . ($this->description ? ' ' . $this->description->render() : '');
    }
}
