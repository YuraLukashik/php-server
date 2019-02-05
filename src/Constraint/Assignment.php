<?php

namespace Analysis\Constraint;


use Analysis\Constraints;

class Assignment extends AnyVertex
{
    public function applyConstraints(Constraints $constraints)
    {
        if (in_array($constraints, $this->appliedConstraints)) {
            return;
        }
        parent::applyConstraints($constraints);
        foreach ($this->children as $child) {
            $child->applyConstraints($constraints);
        }
    }
}