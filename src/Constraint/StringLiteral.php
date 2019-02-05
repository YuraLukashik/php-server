<?php

namespace Analysis\Constraint;


use Analysis\Constraints;
use Analysis\Type\StringPrimitive;

class StringLiteral extends AnyVertex
{
    public function applyConstraints(Constraints $constraints)
    {
        if (in_array($constraints, $this->appliedConstraints)) {
            return;
        }
        parent::applyConstraints($constraints);
        $this->parent->applyConstraints($constraints);
    }

    public function constraints(): Constraints
    {
        return Constraints::single(new StringPrimitive());
    }
}