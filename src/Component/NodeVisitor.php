<?php

namespace Analysis\Component;


use Analysis\Domain\Graph;
use Analysis\Domain\ClassUnit;
use Analysis\Domain\FQCN;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeVisitorAbstract;

class NodeVisitor extends NodeVisitorAbstract
{
    public function __construct(
        Graph $inheritanceGraph,
        Graph $referencesGraph
    )
    {
        $this->inheritanceGraph = $inheritanceGraph;
        $this->referencesGraph = $referencesGraph;
    }

    public function enterNode(Node $node)
    {
        if($node instanceof Node\Stmt\Class_) {
            $this->enterClass($node);
        } elseif ($node instanceof Node\Stmt\Namespace_) {
            $this->enterNamespace($node);
        } elseif ($node instanceof Node\Expr\New_) {
            $this->enterNew($node);
        } elseif ($node instanceof Node\Expr\StaticCall) {
            if ($node->class instanceof Node\Name) {
                $this->enterReference($node->class);
            }
        } elseif ($node instanceof Node\Name\FullyQualified) {
            $this->enterReference($node);
        }
    }

    private function enterReference(Node\Name $name)
    {
        if (!$this->class) {
            return;
        }
        $to = new ClassUnit(FQCN::fromString($name->toString()));
        $this->referencesGraph->addClass($to);
        $this->referencesGraph->addDependency($this->class, $to);
    }

    private function enterNew(Node\Expr\New_ $node)
    {
        if (!$node->class instanceof Node\Name\FullyQualified) {
            return;
        }
        if (!$this->class) {
            return;
        }
        $to = ClassUnit::fromFullyQualified($node->class);
        $this->referencesGraph->addClass($to);
        $this->referencesGraph->addDependency($this->class, $to);
    }

    private function enterClass(Class_ $node)
    {
        if($this->namespace) {
            $fqcn = FQCN::fromParts([$this->namespace, $node->name]);
        } else {
            $fqcn = FQCN::fromParts([$node->name]);
        }
        $class = new ClassUnit($fqcn);
        $this->inheritanceGraph->addClass($class);
        $this->referencesGraph->addClass($class);
        if($node->extends) {
            $extendsClass = new ClassUnit(FQCN::fromString($node->extends->toString()));
            $this->inheritanceGraph->addClass($extendsClass);
            $this->inheritanceGraph->addDependency($extendsClass, $class);
        }
        if ($node->implements) {
            foreach ($node->implements as $name) {
                $implementsClass = new ClassUnit(FQCN::fromString($name->toString()));
                $this->inheritanceGraph->addClass($implementsClass);
                $this->inheritanceGraph->addDependency($implementsClass, $class);
            }
        }
        $this->class = $class;
    }
    
    private function enterNamespace(Namespace_ $node)
    {
        $this->namespace = $node->name->toString();
    }
    
    private $class;
    
    private $namespace;

    private $inheritanceGraph;

    private $referencesGraph;
}
