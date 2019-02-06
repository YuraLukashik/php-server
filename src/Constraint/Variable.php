<?php

namespace Analysis\Constraint;


use Analysis\Constraints;
use Analysis\Scope;
use PhpParser\Node\Name;

class Variable implements Vertex
{
    /** @var Vertex */
    private $parent;

    /** @var string */
    private $name;

    /** @var Constraints */
    private $constraints;

    /** @var Scope */
    private $scope;

    public static function build(Vertex $parent, string $name, Scope $scope)
    {
        $var = new static();
        $var->parent = $parent;
        $var->name = $name;
        $var->constraints = $var->constraints();
        $var->scope = $scope;
        $scope->addVariable($name, $var->constraints);
        return $var;
    }

    public function applyConstraints(Vertex $source, Constraints $constraints)
    {
        if ($source !== $this) {
            $this->constraints = $this->constraints->and($constraints);
            $this->scope->addVariable($this->name, $this->constraints);
        }
        $source !== $this->parent && $this->parent->applyConstraints($this, $constraints);
    }

    public function addChild(Vertex $child)
    {
    }

    public function print(string $prefix = "")
    {
        print($prefix."{$this->name} {$this->constraints->toString()}\n");
    }

    public function produceConstraints(): Vertex
    {
        $this->applyConstraints($this, $this->constraints());
        return $this;
    }

    public function constraints(): Constraints
    {
        return Constraints::any();
    }
}