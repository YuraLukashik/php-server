<?php

namespace Analysis;


use Analysis\Constraint\AnyVertex;
use Analysis\Constraint\Assignment;
use Analysis\Constraint\Multiplication;
use Analysis\Constraint\StringLiteral;
use Analysis\Constraint\Vertex;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\Mul;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class SyntaxGraph extends NodeVisitorAbstract
{
    /** @var Vertex[] */
    private $path = [];

    public function __construct()
    {
        $this->path[] = new AnyVertex();
    }

    public static function build($nodes): Vertex
    {
        $traverser = new NodeTraverser();
        $visitor = new self();
        $traverser->addVisitor($visitor);
        $traverser->traverse($nodes);
        return $visitor->path[0];
    }

    public function enterNode(\PhpParser\Node $node)
    {
        $n = new AnyVertex($this->parent(), $node);
        if ($node instanceof Mul) {
            $n = new Multiplication($this->parent(), $node);
        } elseif ($node instanceof Assign) {
            $n = new Assignment($this->parent(), $node);
        } elseif ($node instanceof String_) {
            $n = new StringLiteral($this->parent(), $node);
        }
        $this->parent()->addChild($n);
        $this->path[] = $n;
    }

    public function leaveNode(\PhpParser\Node $node)
    {
        array_pop($this->path);
    }

    private function parent(): Vertex
    {
        return $this->path[count($this->path) - 1];
    }
}