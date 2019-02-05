<?php

namespace Analysis\Constraint;


use Analysis\Constraints;
use Analysis\Type\Number;

class Multiplication extends AnyVertex
{
    public function applyConstraints(Constraints $constraints)
    {
        if (in_array($constraints, $this->appliedConstraints)) {
            return;
        }
        parent::applyConstraints($constraints);
        $this->parent->applyConstraints($constraints);
        foreach ($this->children as $child) {
            $child->applyConstraints($constraints);
        }
    }

    public function constraints(): Constraints
    {
        return Constraints::single(new Number());
    }
}