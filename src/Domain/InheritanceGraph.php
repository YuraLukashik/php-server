<?php

namespace Analysis\Domain;


class InheritanceGraph
{
    private $graph;
    public function __construct(Graph $graph)
    {
        $this->graph = $graph;
    }
    public function findAllExtending(ClassUnit $class)
    {
        return $this->graph->findConnectedComponent($class);
    }
    public function findAllNotExtended()
    {
        return $this->graph->finalVertices();
    }
    public function findWithMinimumChildren(int $n)
    {
        return $this->graph->verticesWithMinDegree($n);
    }
    /**
     * @throws \Exception
     */
    public function similarClass(ClassUnit $class)
    {
        return $this->graph->similarClass($class);
    }

    public function classes()
    {
        return $this->graph->classes();
    }
}