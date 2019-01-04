<?php

namespace Analysis\Type;


class Number implements Primitive
{
    public function name(): string
    {
        return 'number';
    }
}