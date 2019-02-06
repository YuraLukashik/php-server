<?php

namespace Analysis\Constraint;


use Analysis\Constraints;
use Analysis\Type\Number;

class Multiplication implements Vertex
{
    /** @var Vertex */
    private $left;

    /** @var Constraints */
    private $leftConstraints;

    /** @var Vertex */
    private $right;

    /** @var Constraints */
    private $rightConstraints;

    /** @var Vertex */
    private $result;

    /** @var Constraints */
    private $resultConstraints;

    public static function withResult(Vertex $result): Vertex
    {
        $mul = new self();
        $mul->result = $result;
        $mul->resultConstraints = Constraints::single(new Number());
        $mul->leftConstraints = Constraints::single(new Number());
        $mul->rightConstraints = Constraints::single(new Number());
        return $mul;
    }

    /**
     * @throws \Exception
     */
    public function addChild(Vertex $child)
    {
        if (!$this->left) {
            $this->left = $child;
            return;
        }
        if (!$this->right) {
            $this->right = $child;
            return;
        }
        throw new \Exception('node is full');
    }

    public function applyConstraints(Vertex $source, Constraints $constraints)
    {
        if ($source === $this) {
            $this->left->applyConstraints($this, $constraints);
            $this->right->applyConstraints($this, $constraints);
            $this->result->applyConstraints($this, $constraints);
        }
        if ($source === $this->left) {
            $this->leftConstraints = $this->leftConstraints->and($constraints);
        }
        if ($source === $this->right) {
            $this->rightConstraints = $this->rightConstraints->and($constraints);
        }
        if ($source === $this->result) {
            $this->resultConstraints = $this->resultConstraints->and($constraints);
        }
    }

    public function constraints(): Constraints
    {
        return Constraints::single(new Number());
    }

    public function print(string $prefix = "")
    {
        print($prefix."* :: {$this->leftConstraints->toString()} -> {$this->rightConstraints->toString()} -> {$this->resultConstraints->toString()}\n");
        $this->left->print('   '.$prefix);
        $this->right->print('   '.$prefix);
    }

    public function produceConstraints(): Vertex
    {
        $this->applyConstraints($this, $this->constraints());
        $this->left->produceConstraints();
        $this->right->produceConstraints();
        return $this;
    }
}