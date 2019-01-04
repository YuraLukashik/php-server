<?php

namespace Analysis;


class Set
{
    private $map = [];
    public function __construct(array $elements = [])
    {
        foreach ($elements as $element) {
            $this->map[(string)$element] = $element;
        }
    }

    public function has($element)
    {
        return array_key_exists((string)$element, $this->map);
    }

    public function add($element)
    {
        $this->map[(string)$element] = $element;
    }

    public function remove($element)
    {
        if (!$this->has($element)) {
            return;
        }
        unset($this->map[(string)$element]);
    }

    public function intersect(Set $set): Set
    {
        $elements = [];
        foreach ($this->map as $key => $element) {
            if (array_key_exists($key, $set->map)) {
                $elements[] = $element;
            }
        }
        return new Set($elements);
    }

    public function duplicate(): Set
    {
        return new Set($this->map);
    }

    public function toArray()
    {
        return array_values($this->map);
    }

    public function __toString()
    {
        $res = '';
        foreach ($this->map as $key => $value) {
            $res .= $key . "\n";
        }
        return $res;
    }
}