<?php

namespace Analysis;


use Analysis\Type\Number;
use Analysis\Type\ObjectConstraint;
use Analysis\Type\Primitive;
use Analysis\Type\StringPrimitive;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class TypeInference extends NodeVisitorAbstract
{
    private $spaces = 0;
    public $scope;

    public function __construct()
    {
        $this->scope = new Scope();
    }

    /**
     * @param Node[] $nodes
     */
    public static function infer($nodes): Scope
    {
        $traverser = new NodeTraverser();
        $visitor = new self();
        $traverser->addVisitor($visitor);
        $traverser->traverse($nodes);
        return $visitor->scope;
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Expr\Assign) {
            $this->assign($node);
        } elseif ($node instanceof Node\Stmt\If_) {
            $this->scope = $this->scope->union(
                TypeInference::infer($node->stmts)
            );
            if ($node->else) {
                $this->scope = $this->scope->union(
                    TypeInference::infer($node->else->stmts)
                );
            }
            foreach ($node->elseifs as $elseif) {
                $this->scope = $this->scope->union(
                    TypeInference::infer($elseif->stmts)
                );
            }
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        } elseif ($node instanceof Node\Expr\AssignOp\Mul)
        {
            dump($node);
        }
        $space = join('', array_map(function () {
            return ' ';
        }, range(0, $this->spaces)));
        dump($space.$node->getType());
        $this->spaces += 2;
    }

    public function assign(Node\Expr\Assign $node)
    {
        /** @var Node\Expr\Variable $variable */
        $variable = $node->var;
        $name = $variable->name;
        assert(is_string($name));
        $this->scope->addVariable($name, $this->expressionConstraints($node->expr));
    }

    function expressionConstraints(Node\Expr $expr)
    {
        if ($expr instanceof Node\Scalar\DNumber || $expr instanceof Node\Scalar\LNumber) {
            return Constraints::single(new Number());
        }
        if ($expr instanceof Node\Scalar\String_) {
            return Constraints::single(new StringPrimitive());
        }
        if ($expr instanceof Node\Expr\Variable) {
            $name = $expr->name;
            assert(is_string($name));
            return $this->scope->variable($name)->assign();
        }
        if ($expr instanceof Node\Expr\Assign) {
            $this->assign($expr);
            return $this->scope->variable($expr->var->name)->assign();
        }
        if ($expr instanceof Node\Expr\New_) {
            $name = $expr->class;
            assert($name instanceof Node\Name);
            return Constraints::single(
                new ObjectConstraint(join('/', $name->parts), [new StringPrimitive()])
            );
        }
        if ($expr instanceof Node\Expr\BinaryOp\Mul) {
            return Constraints::single(new Number());
        }
    }

    function assignConstraints(Constraints $type)
    {
        if ($type instanceof Primitive) {
            return clone $type;
        }
        return $type;
    }

    public function leaveNode(Node $node)
    {
        $this->spaces -= 2;
    }
}