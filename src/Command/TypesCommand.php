<?php

namespace Analysis\Command;


use Analysis\Scope;
use Analysis\SyntaxGraph;
use Analysis\TypeInference;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TypesCommand extends Command
{
    public function configure()
    {
        $this->setName('types')
            ->addArgument('path', InputArgument::REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $path = $input->getArgument('path');
        if (is_file($path)) {
            $objects = [$path => $path];
        } else {
            $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
        }
        foreach($objects as $name => $object){
            if(preg_match('/^.*\.php$/', $name)) {
                $fileContent = file_get_contents($name);
                $ast = $parser->parse($fileContent);
//                echo(TypeInference::infer($ast)->toString());
                $scope= new Scope();
                $graph = SyntaxGraph::build($ast, $scope)->produceConstraints();
                $graph->print();
                dump($scope->toString());
            }
        }
    }

}