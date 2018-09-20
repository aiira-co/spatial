<?php
use Lynq\Core\Program;

$apiRoutes = [
  [
    'path'=>'values',
    'controller'=>'ValuesController'
  ],
  [
    'path'=>'mediaz',
    'controller'=>'MediaController',
    'authguard'=> ['AuthenticationModel']
  ]
];


$apiRouterModule = Program::getInstance('Routes');
$apiRouterModule->setRouter($apiRoutes);
