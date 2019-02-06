<?php

namespace Analysis;


use Analysis\Constraint\AnyVertex;
use Analysis\Constraint\Assignment;
use Analysis\Constraint\Multiplication;
use Analysis\Constraint\Scalar;
use Analysis\Constraint\Variable;
use Analysis\Constraint\Vertex;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\Mul;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Stmt\If_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class SyntaxGraph extends NodeVisitorAbstract
{
    /** @var Vertex[] */
    private $path = [];

    public $scopes;

    public function __construct(Scope $scope)
    {
        $this->path[] = new AnyVertex();
        $this->scopes[] = $scope;
    }

    public static function build($nodes, Scope $scope): Vertex
    {
        $traverser = new NodeTraverser();
        $visitor = new self($scope);
        $traverser->addVisitor($visitor);
        $traverser->traverse($nodes);
        return $visitor->path[0];
    }

    public function enterNode(\PhpParser\Node $node)
    {
        $n = new AnyVertex($this->parent(), $node);
        if ($node instanceof Mul) {
            $n = Multiplication::withResult($this->parent());
        } elseif ($node instanceof Assign) {
            $n = Assignment::build($this->parent());
        } elseif ($node instanceof \PhpParser\Node\Scalar || $node instanceof ConstFetch) {
            $n = Scalar::passedTo($this->parent(), $node);
        } elseif ($node instanceof \PhpParser\Node\Expr\Variable) {
            $n = Variable::build($this->parent(), $node->name, $this->scope());
        } elseif ($node instanceof If_) {
            $this->scopes[] = $this->scope()->cloneUnion();
        }
        $this->parent()->addChild($n);
        $this->path[] = $n;
    }

    public function leaveNode(\PhpParser\Node $node)
    {
        array_pop($this->path);
        if ($node instanceof If_) {
            array_pop($this->scopes);
            $this->replaceTopScope($this->scope()->cloneCovering());
        }
    }

    private function parent(): Vertex
    {
        return $this->path[count($this->path) - 1];
    }

    private function replaceTopScope(Scope $scope)
    {
        $this->scopes[count($this->scopes) - 1] = $scope;
    }

    private function scope(): Scope
    {
        return $this->scopes[count($this->scopes) - 1];
    }
}