<?php

/**
 * Class HookLined
 */
class HookLined
{

    /**
     * @var HookNode
     */
    private $header;

    public function __construct()
    {
        $this->header = null;
    }


    /**
     * @param HookNode|HookLined $node
     */
    public function push($node)
    {
        assert($node instanceof HookNode || $node instanceof HookLined);

        if ($node instanceof HookLined) {
            $node = $node->header;
        }


        $cur = $this->header;
        if ($cur) {
            while ($cur->getNext()) {
                $cur = $cur->getNext();
            }
            $cur->setNext($node);
        } else {
            $this->header = $node;
        }
    }


    public function pop()
    {
        $cur = $this->header;
        if ($cur) {
            while ($next = $cur->getNext()) {
                if (!$next->getNext()) {
                    $cur->setNext(null);
                    return $next;
                }
                $cur = $cur->getNext();
            }
            $this->header = null;
            return $cur;
        } else {
            return $cur;
        }
    }


    /**
     * @return HookNode|null
     */
    public function shift()
    {
        $cur = $this->header;
        if ($cur) {
            $header = $cur->getNext();
            $this->header = $header;
            return $cur;
        }
        return $cur;
    }


    public function unshift($node)
    {
        assert($node instanceof HookNode || $node instanceof HookLined);
        if ($node instanceof HookLined) {
            $node = $node->header;
        }
        $node->setNext($this->header);
        $this->header = $node;
    }


    public function isEmpty()
    {
        return !isset($this->header);
    }

    public function clear()
    {
        while ($node = $this->pop()) {
            unset($node);
        }
    }


    public function __destruct()
    {
        $this->clear();
    }

}