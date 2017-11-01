<?php

/**
 * Class Object
 */
class Object
{


    /**
     * @return static
     */
    final static function newInstance()
    {
        $class = get_called_class();
        return new CallLink($class, func_get_args());
    }

    /**
     * @param array $args
     * @return static
     */
    final static function newInstanceArgs($args = [])
    {
        $class = get_called_class();
        return new CallLink($class, $args);
    }


}