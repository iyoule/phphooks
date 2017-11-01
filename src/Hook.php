<?php

/**
 * Class Hook
 */
class Hook
{


    /**
     * @var HookLined[]
     */
    private static $_hooks = [];
    private static $_procs = [];

    /**
     * hook指定函数货类名称
     *
     * $source ['set'] hook调用{上下文对象}set方法  等同于 [$this,'set']
     * $source [$object,'set']  hook $object对象的set方法
     * $source "class::method"  hook "class::method" 这个静态方法
     * $source "function"  hook "function" 函数
     *
     *
     *
     * $hookproc ['set_proc']       用调用{上下文对象}::set_proc方法去hook 参数1 等同于   [$this,'set_proc]
     * $hookproc [$object,'set_proc'] 用$object::set_proc方法去hook 参数1
     * $hookproc "class::method"   用"class::method"方法去hook 参数1
     * $hookproc "function"   用"function"方法去hook 参数1
     *
     * Hook::set(['set'],'fn_set');
     * Hook::set([$object,'set'],'fn_set');
     * Hook::set('Test::set','fn_set');
     * Hook::set('func_set','fn_set');
     *
     *
     *
     *
     * @param string|array $source 准备hook的函数或方法。
     * @param string|array $hookproc 准备hook的函数或方法。
     * @return bool
     */
    public static function set($source, $hookproc)
    {
        $source = self::_parseProcess($source);
        $hookproc = self::_parseProcess($hookproc);
        $sourceKey = self::_parseProcessId($source);
        $procKey = self::_parseProcessId($hookproc);
        $hook = isset(self::$_hooks[$sourceKey]) ? self::$_hooks[$sourceKey] : null;
        if (empty($hook)) {
            $hook = new HookLined();
            $hook->push(new HookNode($source));
        }
        $hook->unshift(new HookNode($hookproc));


        self::$_procs[$procKey] = $sourceKey;
        self::$_hooks[$sourceKey] = $hook;
        return true;
    }


    /**
     * 将传入的process转化成正常可调用的process
     * @param $process
     * @return array|Closure|string
     */
    private static function _parseProcess($process)
    {
        assert(is_string($process) || (is_array($process) && count($process) <= 2) || $process instanceof Closure);
        if (is_array($process)) {
            if (!isset($process[1])) {
                $debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT, 3);
                $debug = array_pop($debug);
                $object = isset($debug['object']) ? $debug['object'] : null;
                $process = [$object, $process[0]];
            } else {
                if ($process[0] instanceof CallLink) {
                    $ref = new ReflectionObject($process[0]);
                    $property = $ref->getProperty('object');
                    $property->setAccessible(true);
                    $process[0] = $property->getValue($process[0]);
                }
            }
        } else if (is_string($process)) {
            if (strpos($process, '::') !== false) {
                list($class, $method) = explode('::', $process);
                if (empty($class)) {
                    $debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT, 3);
                    $debug = array_pop($debug);
                    $object = isset($debug['object']) ? $debug['object'] : null;
                    $process = [$object, $method];
                }
            }
        }
        return $process;
    }


    /**
     * 将process转换成字符串
     * @param $process
     * @return array|Closure|string
     */
    private static function _parseProcessId($process)
    {
        assert(is_string($process) || (is_array($process) && count($process) == 2) || $process instanceof Closure);
        if (is_array($process)) {
            if (is_object($process[0])) {
                $process[0] = spl_object_hash($process[0]);
            }
            $process = join('::', $process);
        } else if ($process instanceof Closure) {
            $process = spl_object_hash($process);
        }
        return $process;
    }


    /**
     * 调用下一步hook链
     * @return mixed
     */
    static function callNextStep()
    {
        if (!self::has(self::$thisProcessId) &&
            isset(self::$_procs[self::$thisProcessId])
        ) {
            self::$thisProcessId = self::$_procs[self::$thisProcessId];
        }
        if (self::has(self::$thisProcessId)) {
            $hook = self::$_hooks[self::$thisProcessId]->shift();
            return call_user_func_array($hook->getCall(), func_get_args());
        }
    }

    /**
     * 调用process的第一步hook连
     * @param $process
     * @param $args
     * @return mixed
     */
    static function callFirstStep($process, $args)
    {
        $processId = self::_parseProcessId($process);
        $hook = self::$_hooks[$processId]->shift();
        self::$thisProcessId = $processId;
        return call_user_func_array($hook->getCall(), $args);
    }

    private static $thisProcessId = null;


    /**
     * @see this::has()
     */
    static function hasHook($process)
    {
        return self::has($process);
    }


    static function getProcessId($process)
    {
        $processId = self::_parseProcessId($process);
        return $processId;
    }

    /**
     * 判断process是否存在hook连
     * @param $process
     * @return bool
     */
    static function has($process)
    {
        $processId = self::_parseProcessId($process);
        if (isset(self::$_hooks[$processId])) {
            if (self::$_hooks[$processId]->isEmpty()) {
                unset(self::$_hooks[$processId]);
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

}