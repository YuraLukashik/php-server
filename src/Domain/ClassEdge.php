<?php

namespace Analysis\Domain;


class ClassEdge
{
    
    public function __construct(ClassUnit $from, ClassUnit $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function from()
    {
        return $this->from;
    }

    public function to()
    {
        return $this->to;
    }

    public function toArray()
    {
        return [
            'from' => (string)$this->from->fqcn(),
            'to' => (string)$this->to->fqcn()
        ];
    }
    
    private $from;
    
    private $to;
}