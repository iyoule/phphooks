<?php


include "../src/Init.php";


class X extends Object
{

    public function __construct()
    {
//        Hook::set(['get'], 'get');
        //  Hook::set([$this, 'get'], 'get');
        Hook::set([$this, 'get'], 'get');
        Hook::set('get', 'xget');
//
        //       Hook::set(['get'], 'get');
//        Hook::set([$this, 'get'], 'get');
//        Hook::set('X::get', ['set']);
//        Hook::set('get', ['X:set']);
    }

    public function get($aaaa)
    {
        echo __METHOD__,"\n";
        return $aaaa;
    }
}

function get($ssss)
{
    echo 'function::get', "\r\n";
    return Hook::callNextStep($ssss);
}

function xget($ssss)
{
    echo 'function::sget', "\r\n";
    return Hook::callNextStep($ssss);
}


//Hook::set('X::get', 'get');
$x = X::newInstance();


var_dump($x->get(2222));