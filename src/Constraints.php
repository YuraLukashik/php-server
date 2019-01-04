<?php

namespace Analysis;


use Analysis\Type\Primitive;

class Constraints
{
    private $constraints;

    /**
     * @param Constraint[] $constraints
     */
    public function __construct($constraints)
    {
        $this->constraints = $constraints;
    }

    public static function single(Constraint $constraint)
    {
        return new self([$constraint]);
    }

    public function assign(): Constraints
    {
        return new static(
            array_map(function (Constraint $constraint) {
                return $constraint instanceof Primitive
                    ? clone $constraint
                    : $constraint;
            }, $this->constraints)
        );
    }

    public function toString(): string
    {
        return join('&', array_map(
            function (Constraint $constraint) {
                return $constraint->name();
            }, $this->constraints
        ));
    }
}