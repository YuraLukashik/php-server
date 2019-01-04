<?php

namespace Analysis\Type;


class StringPrimitive implements Primitive
{
    public function name(): string
    {
        return 'string';
    }
}