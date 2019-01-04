#!/usr/bin/env php
<?php

use Analysis\Command\TestCommand;
use Analysis\Command\TypesCommand;

require_once 'vendor/autoload.php';

$app = new \Symfony\Component\Console\Application();
$app->add(new TestCommand());
$app->add(new TypesCommand());
$app->run();


