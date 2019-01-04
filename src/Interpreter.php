<?php

namespace Analysis;


use Analysis\Domain\InheritanceGraph;
use Analysis\Domain\ReferencesGraph;

class Interpreter
{
    /** @var InheritanceGraph */
    private $inheritanceGraph;

    /** @var ReferencesGraph */
    private $referencesGraph;

    public function __construct(
        InheritanceGraph $inheritanceGraph,
        ReferencesGraph $referencesGraph
    )
    {
        $this->inheritanceGraph = $inheritanceGraph;
        $this->referencesGraph = $referencesGraph;
    }

    /**
     * @param Criteria[] $criterias
     * @return Set
     * @throws \Exception
     */
    public function find(array $criterias): Set
    {
        $matched = $this->inheritanceGraph->classes()->duplicate();
        foreach ($criterias as $criteria) {
            if ($criteria instanceof ExtendsClass) {
                $matchedCriteria = $this->inheritanceGraph->findAllExtending($this->inheritanceGraph->similarClass($criteria->class()));
            } elseif ($criteria instanceof NothingExtends) {
                $matchedCriteria = $this->inheritanceGraph->findAllNotExtended();
            } elseif ($criteria instanceof MinimumChildren) {
                $matchedCriteria = $this->inheritanceGraph->findWithMinimumChildren($criteria->number());
            } elseif ($criteria instanceof NothingRefers) {
                $matchedCriteria = $this->referencesGraph->findNotReferenced();
            } else {
                $matchedCriteria = $matched;
            }
            $matched = $matched->intersect($matchedCriteria);
        }
        return $matched;
    }
}