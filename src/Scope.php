<?php

namespace Analysis;


use Analysis\Type\Union;

class Scope
{
    public $variables = [];
    public $classes = [];

    public function addVariable(string $name, Constraints $constraints)
    {
        $this->variables[$name] = $constraints;
    }

    public function variable(string $name): Constraints
    {
        return $this->variables[$name];
    }

    public function union(Scope $scope): Scope
    {
        $newScope = clone $this;
        foreach ($scope->variables as $name => $constraints) {
            if (!isset($this->variables[$name])) {
                $newScope->variables[$name] = $constraints;
                continue;
            }
            $newScope->variables[$name] = Constraints::single(
                Union::of(
                    $this->variables[$name],
                    $scope->variables[$name]
                )
            );
        }
        return $newScope;
    }

    public function toString(): string
    {
        $scope = "variables:\n";
        foreach ($this->variables as $name => $constraints) {
            $scope .= "  $name: ".$constraints->toString()."\n";
        }
        return $scope;
    }
}