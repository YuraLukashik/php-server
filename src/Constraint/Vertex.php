<?php

namespace Analysis\Constraint;


use Analysis\Constraints;

interface Vertex
{
    public function addChild(Vertex $child);

    public function print(string $prefix = "");

    public function produceConstraints(): Vertex;

    public function applyConstraints(Vertex $source, Constraints $constraints);

    public function constraints(): Constraints;
}