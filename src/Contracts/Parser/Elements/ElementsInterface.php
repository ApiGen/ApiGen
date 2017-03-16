<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Elements;

interface ElementsInterface
{

    const CLASSES = 'classes';
    const CONSTANTS = 'constants';
    const EXCEPTIONS = 'exceptions';
    const FUNCTIONS = 'functions';
    const INTERFACES = 'interfaces';
    const TRAITS = 'traits';
    const PROPERTIES = 'properties';
    const METHODS = 'methods';


    /**
     * @return string[]
     */
    public function getClassTypeList(): array;


    /**
     * @return string[]
     */
    public function getAll(): array;


    /**
     * @return array[]
     */
    public function getEmptyList(): array;
}
