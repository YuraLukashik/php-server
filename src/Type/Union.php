<?php

namespace Analysis\Type;


use Analysis\Constraint;
use Analysis\Constraints;

class Union implements Constraint
{
    private $constraints;
    public static function of(Constraints ...$constraints)
    {
        $union = new self();
        $union->constraints = $constraints;
        return $union;
    }

    public function name(): string
    {
        return join('|', array_map(function (Constraints $constraints) {
            return $constraints->toString();
        }, $this->constraints));
    }
}