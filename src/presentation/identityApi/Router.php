<?php
use Cqured\Core\Program;

$apiRoutes = [
    [
        'path' => 'account',
        'controller' => 'AccountController',
    ],
    [
        'path' => 'account/signin',
        'controller' => 'AuthorizationController',
    ],
    [
        'path' => 'account/signup',
        'controller' => 'AccountController',
    ],
    [
        'path' => 'pay',
        'controller' => 'HubtelcallbackController',
    ],
];

$apiRouterModule = Program::getInstance('Routes');
$apiRouterModule->setRouter($apiRoutes);
