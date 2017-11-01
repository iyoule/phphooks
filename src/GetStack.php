<?php

/**
 * Class GetCall
 */
class GetStack
{


    public static function stack()
    {
        $ret = debug_backtrace();
        array_shift($ret);
        array_shift($ret);
        return $ret;
    }

    private static function _stack($option = 0)
    {
        $debug = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT | $option, 4);
        $ret = array_pop($debug);
        unset($debug);
        return $ret;
    }


    public static function object()
    {
        $object = self::_stack(DEBUG_BACKTRACE_IGNORE_ARGS);
        return isset($object['object']) ? $object['object'] : null;
    }

    public static function classed()
    {
        $object = self::_stack(DEBUG_BACKTRACE_IGNORE_ARGS);
        return isset($object['class']) ? $object['class'] : null;
    }

    public static function func()
    {
        $object = self::_stack(DEBUG_BACKTRACE_IGNORE_ARGS);
        return isset($object['function']) ? $object['function'] : null;
    }

    public static function method()
    {
        $object = self::_stack(DEBUG_BACKTRACE_IGNORE_ARGS);
        return isset($object['function']) ? $object['function'] : null;
    }

    public static function args()
    {
        $object = self::_stack();
        return isset($object['args']) ? $object['args'] : null;
    }

    public static function type()
    {
        $object = self::_stack(DEBUG_BACKTRACE_IGNORE_ARGS);
        return isset($object['type']) ? $object['type'] : null;
    }

    public static function file()
    {
        $object = self::_stack(DEBUG_BACKTRACE_IGNORE_ARGS);
        return isset($object['file']) ? $object['file'] : null;
    }

    public static function line()
    {
        $object = self::_stack(DEBUG_BACKTRACE_IGNORE_ARGS);
        return isset($object['line']) ? $object['line'] : null;
    }


}