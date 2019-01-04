<?php

namespace Analysis\Command;

use Analysis\Component\NodeVisitor;
use Analysis\Domain\Graph;
use Analysis\Domain\ClassMap;
use Analysis\Domain\ClassUnit;
use Analysis\Domain\FQCN;
use Analysis\Domain\InheritanceGraph;
use Analysis\Domain\ReferencesGraph;
use Analysis\ExtendsClass;
use Analysis\Interpreter;
use Analysis\Parser;
use DI\ContainerBuilder;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Twig_Extension_Debug;

class TestCommand extends Command
{
    
    public function configure()
    {
        $this->setName('analyse')
            ->addArgument('path', InputArgument::REQUIRED);
    }
    
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $inheritanceGraph = new Graph();
        $referencesGraph = new Graph();
        $twig = $this->configureTwig();

        $path = $input->getArgument('path');
        $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
        foreach($objects as $name => $object){
            if(preg_match('/^.*\.php$/', $name)) {
                $fileContent = file_get_contents($name);
                $ast = $parser->parse($fileContent);

                $nameResolver = new NameResolver;
                $traverser = new NodeTraverser();
                $traverser->addVisitor($nameResolver);
                $traverser->addVisitor(new NodeVisitor($inheritanceGraph, $referencesGraph));
                $traverser->traverse($ast);
            }
        }
        $criterias = Parser::parse(file_get_contents('./query.cgq'));
        $interpreter = new Interpreter(
            new InheritanceGraph($inheritanceGraph),
            new ReferencesGraph($referencesGraph)
        );
        $classes = $interpreter->find($criterias);
        $output->writeln((string)$classes);

        $f = fopen('output.html', 'w');
        fwrite($f, $twig->render('graph.html.twig', ['graph' => $referencesGraph->toArray()]));
        fclose($f);
    }

    /**
     * @return \Twig_Environment
     */
    private function configureTwig()
    {
        $loader = new \Twig_Loader_Filesystem(__DIR__.'/../Template');
        $twig = new \Twig_Environment($loader, ['debug' => true]);
        $twig->addExtension(new Twig_Extension_Debug());
        return $twig;
    }

    /**
     * @param ClassMap $classMap
     * @param Node $node
     */
    private function populateClassMapFromAst(ClassMap $classMap, Node $node)
    {
        var_dump($node);
    }

    /**
     * @return \DI\Container
     */
    private function buildContainer()
    {
        $builder = new ContainerBuilder();
        return $builder->build();
    }

}
