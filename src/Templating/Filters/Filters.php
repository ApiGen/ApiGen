<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters;

/**
 * @todo use ModularFilters package
 */
class Filters
{
    /**
     * Calls public method with args if exists and passes args.
     *
     * @return mixed
     */
    public function loader(string $name)
    {
        if (method_exists($this, $name)) {
            $args = array_slice(func_get_args(), 1);
            return call_user_func_array([$this, $name], $args);
        }

        return null;
    }

    public static function urlize(string $string): string
    {
        return preg_replace('~[^\w]~', '.', $string);
    }

    protected function getTypeName(string $name, bool $trimNamespaceSeparator = true): string
    {
        $names = [
            'int' => 'integer',
            'bool' => 'boolean',
            'double' => 'float',
            'void' => '',
            'FALSE' => 'false',
            'TRUE' => 'true',
            'NULL' => 'null',
            'callback' => 'callable'
        ];

        // Simple type
        if (strlen($name) > 2 && substr($name, -2) === '[]') {
            $clearName = substr($name, 0, -2);
            if (isset($names[$clearName])) {
                return $names[$clearName] . '[]';
            }
        }

        if (isset($names[$name])) {
            return $names[$name];
        }

        // Class, constant or function
        return $trimNamespaceSeparator ? ltrim($name, '\\') : $name;
    }

    protected function url(string $url): string
    {
        return rawurlencode($url);
    }
}
