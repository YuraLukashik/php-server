<?php

namespace Analysis\Domain;


class ReferencesGraph
{
    private $graph;
    public function __construct(Graph $graph)
    {
        $this->graph = $graph;
    }

    public function findNotReferenced()
    {
        return $this->graph->startingVertices();
    }
}