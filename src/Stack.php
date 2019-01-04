<?php

namespace Analysis;


class Stack
{
    private $stack = [];
    public function __construct()
    {
    }
    public function push($element)
    {
        array_push($this->stack, $element);
    }
    public function pop()
    {
        if ($this->empty()) {
            throw new \Exception('empty');
        }
        return array_pop($this->stack);
    }
    public function empty()
    {
        return count($this->stack) === 0;
    }
}