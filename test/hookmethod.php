<?php


include "../src/Init.php";


class hookmethod extends Object
{

    public function __construct()
    {
        Hook::set(['fn1'], function () {
            var_dump('hook->fn1');
            return Hook::callNextStep();
        });
        Hook::set([$this, 'fn2'], function () {
            var_dump('hook->fn2');
            Hook::callNextStep();
        });
    }

    public function fn1()
    {
        var_dump(__METHOD__);
    }

    public function fn2()
    {
        var_dump(__METHOD__);
    }

    public function fn3()
    {
        var_dump(__METHOD__);
    }
}


$hook = hookmethod::newInstance();
$hook->fn1();
$hook->fn2();

Hook::set([$hook, 'f3'], function () {
    var_dump('hook->fn3');
    Hook::callNextStep();
});

$hook->fn3();

