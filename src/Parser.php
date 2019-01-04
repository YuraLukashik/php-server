<?php

namespace Analysis;


use Analysis\Domain\FQCN;

class Parser
{
    /**
     * @param string $query
     * @return Criteria[]
     * @throws \Exception
     */
    public static function parse(string $query): array
    {
        $criterias = [];
        $lines = explode("\n", $query);
        foreach ($lines as $line) {
            if (empty($line)) {
                continue;
            }
            $matches = [];
            if (preg_match("/^[\s]*it[\s]+extends[\s]+(.+)[\s]*$/", $line, $matches)) {
                $criterias[] = new ExtendsClass(FQCN::fromString($matches[1]));
                continue;
            }
            if (preg_match("/^[\s]*it[\s]+implements[\s]+(.+)[\s]*$/", $line, $matches)) {
                $criterias[] = new ExtendsClass(FQCN::fromString($matches[1]));
                continue;
            }
            if (preg_match("/^[\s]*nothing[\s]+extends[\s]+it[\s]*$/", $line)) {
                $criterias[] = new NothingExtends();
                continue;
            }
            if (preg_match("/^[\s]*it[\s]+has[\s]+at[\s]+least[\s]+([0-9]+)[\s]+children[\s]*$/", $line, $matches)) {
                $criterias[] = new MinimumChildren(intval($matches[1]));
                continue;
            }
            if (preg_match("/^[\s]*nothing[\s]+refers[\s]+to[\s]+it[\s]*$/", $line)) {
                $criterias[] = new NothingRefers();
                continue;
            }
            throw new \Exception('could not parse "'.$line.'"');
        }
        return $criterias;
    }
}