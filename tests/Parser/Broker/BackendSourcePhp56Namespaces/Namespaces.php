<?php

namespace some\Name\space
{

    const SOME_CONSTANT = 2 * 3;


    define('OTHER_CONSTANT', 2);


    function someAloneFunction()
    {

    }


    function someOtherFunction()
    {

    }

}


namespace some\Other\space
{

    use function \some\Name\space\someOtherFunction;
    use function \some\Name\space\someAloneFunction as someFunction;
    use const some\Name\space\SOME_CONSTANT;
    use const some\Name\space\OTHER_CONSTANT as SECOND_NAME;


    function someAloneFunction()
    {
        someFunction();
        someOtherFunction();
    }

}

