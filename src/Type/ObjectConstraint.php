<?php

namespace Analysis\Type;


use Analysis\Constraint;

class ObjectConstraint implements Constraint
{
    /** @var Constraint[] */
    public $attributes;
    public $class;

    public function __construct(string $class, array $attributes)
    {
        $this->class = $class;
        $this->attributes = $attributes;
    }

    public function name(): string
    {
        return 'object';
    }
}