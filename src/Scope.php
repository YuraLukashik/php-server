<?php

namespace Analysis;


use Analysis\Type\Union;

class Scope
{
    public $variables = [];

    public $unionScopes = [];

    public $coveringScopes = [];

    public function cloneUnion(): Scope
    {
        $union = new Scope();
        $this->unionScopes[] = $union;
        return $union;
    }

    public function cloneCovering(): Scope
    {
        $covering = new Scope();
        $this->coveringScopes[] = $covering;
        return $covering;
    }

    public function addVariable(string $name, Constraints $constraints)
    {
        $this->variables[$name] = $constraints;
    }

    public function variable(string $name): Constraints
    {
        return $this->variables[$name];
    }

    public function variables(): array
    {
        $variables = $this->variables;
        /** @var Scope $scope */
        foreach ($this->unionScopes as $scope) {
            foreach ($scope->variables() as $name => $constraints) {
                if (array_key_exists($name, $variables)) {
                    $variables[$name] = Constraints::single(Union::of($variables[$name], $constraints));
                } else {
                    $variables[$name] = $constraints;
                }
            }
        }
        foreach ($this->coveringScopes as $scope) {
            foreach($scope->variables() as $name => $constraints) {
                $variables[$name] = $constraints;
            }
        }
        return $variables;
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
        foreach ($this->variables() as $name => $constraints) {
            $scope .= "  $name: ".$constraints->toString()."\n";
        }
        return $scope;
    }
}