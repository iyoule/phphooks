<?php

include "../src/Init.php";

class X extends Object
{


    public function __construct()
    {

    }

    public function test()
    {
        var_dump(__METHOD__);
        return __METHOD__;
    }
}


$new = X::newInstance();

Hook::set('X::test', function () {
    var_dump(__FUNCTION__);
    return Hook::callNextStep(1);
});


$new->test();