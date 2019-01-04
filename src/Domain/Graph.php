<?php

namespace Analysis\Domain;


use Analysis\Set;
use Analysis\Stack;

class Graph
{
    public function __construct()
    {
        $this->classes = new Set();
    }

    public function addClass(ClassUnit $class)
    {
        if(!$this->classes->has($class)) {
            $this->classes->add($class);
            $this->classEdges[(string)$class] = [];
        }
        return $this;
    }

    public function addDependency(ClassUnit $class, ClassUnit $dClass)
    {
        $this->addClass($class)
            ->addClass($dClass);
        $this->edges[] = new ClassEdge($class, $dClass);
        $this->classEdges[(string)$class][] = $dClass;
    }

    public function findConnectedComponent(ClassUnit $class): Set
    {
        if (!$this->classes->has($class)) {
            return new Set();
        }
        $extending = new Set();
        $candidates = new Stack();
        $candidates->push($class);
        while (!$candidates->empty()) {
            $candidate = $candidates->pop();
            if ($extending->has($candidate)) {
                continue;
            }
            $extending->add($candidate);
            foreach ($this->classEdges[(string)$candidate] as $classEdge) {
                $candidates->push($classEdge);
            }
        }
        return $extending;
    }

    public function finalVertices(): Set
    {
        $result = new Set();
        foreach ($this->classes->toArray() as $class) {
            if (empty($this->classEdges[(string)$class])) {
                $result->add($class);
            }
        }
        return $result;
    }

    public function startingVertices(): Set
    {
        $result = $this->classes->duplicate();
        foreach ($this->edges as $edge) {
            $result->remove($edge->to());
        }
        return $result;
    }

    public function verticesWithMinDegree(int $n): Set
    {
        $result = new Set();
        foreach ($this->classes->toArray() as $class) {
            if (count($this->classEdges[(string)$class]) >= $n) {
                $result->add($class);
            }
        }
        return $result;
    }

    public function similarClass(ClassUnit $shortClass): ClassUnit
    {
        $found = [];
        foreach ($this->classes->toArray() as $class) {
            if ($class->endsWith($shortClass)) {
                $found[]= $class;
            }
        }
        if (count($found) > 1) {
            throw new \Exception('"' . (string)$shortClass.'" is ambiguous');
        }
        if (count($found) < 1) {
            throw new \Exception('"' . (string)$shortClass.'" not found');
        }
        return array_pop($found);
    }
    
    public function toArray()
    {
        return [
            'classes' => array_map(function(ClassUnit $class) {
                return $class->toArray();
            }, $this->classes->toArray()),
            'edges' => array_map(function(ClassEdge $edge) {
                return $edge->toArray();
            }, $this->edges)
        ];
    }

    /** @var Set */
    private $classes;

    /**
     * @var ClassEdge[]
     */
    private $edges = [];

    private $classEdges = [];

    public function classes()
    {
        return $this->classes;
    }
}