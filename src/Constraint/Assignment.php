<?php

namespace Analysis\Constraint;


use Analysis\Constraints;

class Assignment implements Vertex
{
    /** @var Vertex */
    private $parent;
    /** @var Vertex */
    private $var;
    /** @var Vertex */
    private $expr;

    public static function build(Vertex $parent)
    {
        $ass = new static();
        $ass->parent = $parent;
        return $ass;
    }

    public function applyConstraints(Vertex $source, Constraints $constraints)
    {
        $source !== $this->parent && $this->parent->applyConstraints($this, $constraints);
        $source !== $this->expr && $this->expr->applyConstraints($this, $constraints);
        $source !== $this->var && $this->var->applyConstraints($this, $constraints);
    }

    public function addChild(Vertex $child)
    {
        if (!$this->var) {
            $this->var = $child;
            return;
        }
        if (!$this->expr) {
            $this->expr = $child;
        }
    }

    public function print(string $prefix = "")
    {
        print($prefix."=\n");
        $this->var->print('   '.$prefix);
        $this->expr->print('   '.$prefix);
    }

    public function produceConstraints(): Vertex
    {
        $this->var->produceConstraints();
        $this->expr->produceConstraints();
        return $this;
    }

    public function constraints(): Constraints
    {
        return Constraints::any();
    }
}