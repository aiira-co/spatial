<?php
use Cqured\Core\Program;

$apiRoutes = [

    [
        'path' => 'values',
        'controller' => 'ValuesController',
    ],
    [
        'path' => 'test',
        'controller' => 'TestController',
    ],
];

$apiRouterModule = Program::getInstance('Routes');
$apiRouterModule->setRouter($apiRoutes);
