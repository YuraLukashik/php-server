<?php

namespace Analysis\Type;


class Number extends Primitive
{
    public function name(): string
    {
        return 'number';
    }
}