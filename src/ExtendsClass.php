<?php

namespace Analysis;


use Analysis\Domain\ClassUnit;
use Analysis\Domain\FQCN;

class ExtendsClass implements Criteria
{
    private $fqcn;
    public function __construct(FQCN $fqcn)
    {
        $this->fqcn = $fqcn;
    }

    public function class(): ClassUnit
    {
        return new ClassUnit($this->fqcn);
    }
}