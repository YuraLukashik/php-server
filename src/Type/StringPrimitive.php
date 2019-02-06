<?php

namespace Analysis\Type;


class StringPrimitive extends Primitive
{
    public function name(): string
    {
        return 'string';
    }
}