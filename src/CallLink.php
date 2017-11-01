<?php

/**
 * Class CallLink
 */
class CallLink
{

    /**
     * @var Reflection|ReflectionFunction|ReflectionObject|ReflectionMethod|ReflectionClass
     */
    private $ref;
    private $object;


    function __construct($class, $args = [])
    {
        assert(is_string($class) || is_object($class));
        if ($this->isFunction($class)) {
            $this->ref = new ReflectionFunction($class);
            $this->object = $class;
        } else if ($class instanceof Reflection) {
            $this->ref = $class;
            $this->object = null;
        } else if (is_object($class)) {
            $this->ref = new ReflectionObject($class);
            $this->object = &$class;
        } else {
            if (strpos($class, '::') !== false) {
                list($_class, $method) = explode('::', $class);
                $o = new ReflectionClass($_class);
                if ($o->hasMethod($method)) {
                    $this->ref = new ReflectionMethod($_class, $method);
                    $this->object = $class;
                }
            } else {
                $this->ref = new ReflectionClass($class);
                $this->object = $this->ref->newInstanceArgs($args);
            }
        }
    }


    private function isFunction($value)
    {
        return (is_string($value) && function_exists($value)) || $value instanceof Closure;
    }


    public function __destruct()
    {
        unset($this->ref, $this->object);
    }


    public function __invoke()
    {
        if ($this->ref instanceof ReflectionFunction) {
            $process = $this->ref->name;
            if ($process == '{closure}') {
                return call_user_func_array($this->object, []);
            } else if (Hook::hasHook($process)) {
                return Hook::callFirstStep($process, func_get_args());
            }
        } else if ($this->ref instanceof ReflectionMethod) {
            $process = $this->object;
            if (Hook::hasHook($process)) {
                return Hook::callFirstStep($process, func_get_args());
            }
            return call_user_func_array($this->object, func_get_args());
        }
        return $this->ref->invokeArgs(func_get_args());
    }


    public function __call($name, $arguments)
    {

        if ($this->ref instanceof ReflectionFunction) {
            return $this->ref->invokeArgs($arguments);
        }
        $process = [$this->object, $name];
        if (Hook::hasHook($process)) {
            return Hook::callFirstStep($process, $arguments);
        }
        $process = get_class($this->object) . '::' . $name;
        if (Hook::hasHook($process)) {
            return Hook::callFirstStep($process, $arguments);
        } else {
            if ($this->ref->hasMethod($name)) {
                $method = $this->ref->getMethod($name);
                return $method->invokeArgs($this->object, $arguments);
            }
            trigger_error("Undefined method $name", E_USER_ERROR);
        }
    }


    public static function __callStatic($name, $arguments)
    {
        trigger_error("Disabled call static method", E_USER_ERROR);
    }

    function __get($name)
    {
        $class = $this->ref->name;
        if ($this->ref->hasProperty($name)) {
            $property = $this->ref->getProperty($name);
            if ($property->isPublic()) {
                return $property->getValue($this->object);
            }
            if ($this->ref->hasMethod('__get')) {
                $method = $this->ref->getMethod('__get');
                if ($method->isPublic()) {
                    return $method->invoke($this->object, $name);
                }
            }

            if ($property->isPrivate()) {
                $error = "Cannot access private property $class::\$$name";
            } else {
                $error = "Cannot access protected property $class::\$$name";
            }
            trigger_error($error, E_USER_ERROR);
        }
        trigger_error("Undefined property: $class::\$$name", E_USER_ERROR);
    }


    function __set($name, $value)
    {
        $class = $this->ref->name;
        if ($this->ref->hasProperty($name)) {
            $property = $this->ref->getProperty($name);
            if ($property->isPublic()) {
                $property->setValue($this->object, $value);
                return;
            }
            if ($this->ref->hasMethod('__set')) {
                $method = $this->ref->getMethod('__set');
                if ($method->isPublic()) {
                    $method->invoke($this->object, $name);
                    return;
                }
            }
            if ($property->isPrivate()) {
                $error = "Cannot access private property $class::\$$name";
            } else {
                $error = "Cannot access protected property $class::\$$name";
            }
            trigger_error($error, E_USER_ERROR);
        }
        trigger_error("Undefined property: $class::\$$name", E_USER_ERROR);
    }

}