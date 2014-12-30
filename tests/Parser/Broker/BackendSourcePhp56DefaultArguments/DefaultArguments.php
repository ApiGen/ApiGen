<?php

const GLOBAL_CONST = 1;


abstract class SomeClass
{

    const CLASS_CONST = 2;

}


function classConst($agr1, $arg2 = SomeClass::CLASS_CONST)
{

}


function globalConst($arg = GLOBAL_CONST)
{

}

