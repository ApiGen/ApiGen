<?php declare(strict_types=1);

namespace ApiGen\Console\Input;

use Symfony;

class LiberalFormatArgvInput extends Symfony\Component\Console\Input\ArgvInput
{

    /**
     * @return array
     */
    public function getOptions()
    {
        $options = parent::getOptions();
        foreach ($options as $key => $value) {
            $options[$key] = $this->removeEqualsSign($value);
            $options[$key] = $this->splitByComma($value);
        }
        return $options;
    }


    /**
     * @param string $name
     * @return mixed
     */
    public function getOption($name)
    {
        $this->options = $this->getOptions();
        return parent::getOption($name);
    }


    /**
     * @param array|string $value
     * @return array|string
     */
    private function removeEqualsSign($value)
    {
        if (is_array($value)) {
            array_walk($value, function (&$singleValue) {
                $singleValue = ltrim((string) $singleValue, '=');
            });
        } else {
            $value = (string) $value;
            $value = ltrim($value, '=');
        }
        return $value;
    }


    /**
     * @param mixed $value
     * @return mixed
     */
    private function splitByComma($value)
    {
        if (is_array($value)) {
            array_walk($value, function (&$singleValue) {
                $singleValue = (string) $singleValue;
                $singleValue = $this->splitByCommaIfHasAny($singleValue);
            });
            if (count($value) && is_array($value[0])) {
                return $value[0];
            }
        } else {
            $value = (string) $value;
            $value = $this->splitByCommaIfHasAny($value);
        }
        return $value;
    }


    private function containsComma(string $value): bool
    {
        return strpos($value, ',') !== false;
    }


    /**
     * @return string|array
     */
    private function splitByCommaIfHasAny(string $value)
    {
        if ($this->containsComma($value)) {
            return explode(',', $value);
        }
        return $value;
    }
}
