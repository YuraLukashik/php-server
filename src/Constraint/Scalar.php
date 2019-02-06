<?php

namespace Analysis\Constraint;


use Analysis\Constraints;
use Analysis\Type\Primitive;
use PhpParser\Node\Expr\ConstFetch;

class Scalar implements Vertex
{
    /** @var Vertex */
    private $parent;

    /** @var Constraints */
    private $constraints;

    /**
     * @param Scalar | ConstFetch $node
     */
    public static function passedTo(Vertex $parent, $node): Vertex
    {
        $scalar = new static();
        $scalar->parent = $parent;
        $scalar->constraints = Constraints::single(Primitive::fromScalar($node));
        return $scalar;
    }
    public function applyConstraints(Vertex $source, Constraints $constraints)
    {
        $source !== $this->parent && $this->parent->applyConstraints($this, $constraints);
    }

    public function constraints(): Constraints
    {
        return $this->constraints;
    }

    public function addChild(Vertex $child)
    {
    }

    public function print(string $prefix = "")
    {
        print($prefix.$this->constraints->toString()."\n");
    }

    public function produceConstraints(): Vertex
    {
        $this->applyConstraints($this, $this->constraints());
        return $this;
    }
}