<?php

namespace Analysis\Type;


use Analysis\Constraint;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Scalar;

abstract class Primitive implements Constraint
{
    /**
     * @param Scalar|ConstFetch $scalar
     * @return Primitive
     */
    public static function fromScalar($scalar): Primitive {
        if ($scalar instanceof Scalar\DNumber || $scalar instanceof Scalar\LNumber) {
            return new Number();
        } elseif ($scalar instanceof Scalar\String_) {
            return new StringPrimitive();
        } elseif ($scalar instanceof ConstFetch && in_array($scalar->name, ['true', 'false'])) {
            return new Boolean();
        }
    }
}

