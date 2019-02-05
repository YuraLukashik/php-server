<?php

namespace Analysis\Constraint;


use Analysis\Constraints;
use PhpParser\Node;

class AnyVertex implements Vertex
{
    /** @var ?Vertex */
    protected $parent;
    /** @var Vertex[] */
    protected $children = [];

    /** @var null|Node */
    private $node;

    /** @var Constraints */
    private $constraints;

    protected $appliedConstraints = [];

    public function __construct(
        ?Vertex $parent = null,
        ?Node $node = null
    )
    {
        $this->parent = $parent;
        $this->node = $node;
        $this->constraints = new Constraints([]);
    }

    public function applyConstraints(Constraints $constraints)
    {
        if (in_array($constraints, $this->appliedConstraints)) {
            return;
        }
        $this->appliedConstraints[] = $constraints;
        $this->constraints = $this->constraints->and($constraints);
    }

    public function produceConstraints(): Vertex
    {
        $this->applyConstraints($this->constraints());
        foreach ($this->children as $child) {
            $child->produceConstraints();
        }
        return $this;
    }

    public function constraints(): Constraints
    {
        return new Constraints([]);
    }

    public function addChild(Vertex $child)
    {
        $this->children[] = $child;
    }

    public function print($prefix = '')
    {
        $name = $this->node ? $this->node->getType() : 'root';
        print ($prefix.$name.$this->constraints->toString()."\n");
        foreach ($this->children as $child) {
            $child->print('  '.$prefix);
        }
    }
}