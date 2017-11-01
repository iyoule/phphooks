<?php

/**
 * Class HookNode
 * hook链节点
 */
class HookNode
{
    private $next;
    private $call;


    public function __construct($call, $next = null)
    {
        if (is_array($call)) {
            $call[0] = new CallLink($call[0]);
        } else {
            $call = new CallLink($call);
        }
        $this->call = $call;
        $this->next = $next;
    }

    /**
     * @return mixed
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * @param mixed $next
     */
    public function setNext($next)
    {
        $this->next = $next;
    }

    /**
     * @return mixed
     */
    public function getCall()
    {
        return $this->call;
    }

    /**
     * @param mixed $call
     */
    public function setCall($call)
    {
        $this->call = $call;
    }


}