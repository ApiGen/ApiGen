<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters;

abstract class Filters
{

    /**
     * Calls public method with args if exists and passes args.
     *
     * @param string $name
     * @throws \Exception
     * @return mixed
     */
    public function loader($name)
    {
        if (method_exists($this, $name)) {
            $args = array_slice(func_get_args(), 1);
            return call_user_func_array([$this, $name], $args);
        }
        return null;
    }


    /**
     * @param string $string
     * @return string
     */
    public static function urlize($string)
    {
        return preg_replace('~[^\w]~', '.', $string);
    }


    /**
     * @param string $name
     * @param bool $trimNamespaceSeparator
     * @return string
     */
    protected function getTypeName($name, $trimNamespaceSeparator = true)
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


    /**
     * @param string $url
     * @return string
     */
    private function url($url)
    {
        return rawurlencode($url);
    }
}
