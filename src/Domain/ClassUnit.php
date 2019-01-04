<?php

namespace Analysis\Domain;


use PhpParser\Node\Name\FullyQualified;

class ClassUnit
{
    
    public function __construct(FQCN $fqcn)
    {
        $this->fqcn = $fqcn;
    }

    public static function fromFullyQualified(FullyQualified $name)
    {
        return new static(FQCN::fromString($name->toString()));
    }

    public function endsWith(ClassUnit $class)
    {
        $s1 = strrev((string)$class);
        $s2 = strrev((string)$this);
        return strpos($s2, $s1) === 0;
    }

    /**
     * @return FQCN
     */
    public function fqcn()
    {
        return $this->fqcn;
    }
    
    public function toArray()
    {
        return [
            'fqcn' => (string)$this->fqcn
        ];
    }

    public function equals(ClassUnit $class)
    {
        return (string)$class->fqcn() === (string)$this->fqcn;
    }

    public function __toString()
    {
        return (string)$this->fqcn;
    }

    private $fqcn;

}