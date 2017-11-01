<?php


function __autoload($class)
{
    static $map = [];

    if (!isset($map[$class])) {
        $map[$class] = true;
        if (is_file(__DIR__ . "/$class.php")) {
            require __DIR__ . "/$class.php";
        }
    }
    return true;
}