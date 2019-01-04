<?php

namespace Analysis\Domain;


class FQCN
{
    
    public static function fromParts(array $parts)
    {
        return new static(implode("\\", $parts));
    }

    public function __construct($fqcn)
    {
        $this->fqcn = $fqcn;
    }

    public static function fromString(string $fqcn)
    {
        return new static($fqcn);
    }

    public function __toString()
    {
        return $this->fqcn;
    }

    private $fqcn;

}