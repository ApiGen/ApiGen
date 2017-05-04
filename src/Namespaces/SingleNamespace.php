<?php declare(strict_types=1);

namespace ApiGen\Namespaces;

final class SingleNamespace
{
    // @todo: consider value objects

    // getName()
    // getClassReflections()
    // getInterfacesReflections()
    // getTraitReflections()
    // getFunctionReflections()

    /**
     * @return string[]
     */
    public function getSubnamesForName(): array
    {
        $allNamespaces = array_keys($this->namespaceStorage->getReflectionsCategorizedToNamespaces());

        return array_filter($allNamespaces, function ($subname) use ($this->name) {
            $pattern = '~^' . preg_quote($this->name) . '\\\\[^\\\\]+$~';
            return (bool) preg_match($pattern, $subname);
        });
    }
}
