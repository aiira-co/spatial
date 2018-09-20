<?php

$apiRoutes = [
  [
    'path'=>'values',
    'controller'=>'values'
  ]
];


$apiRouterModule = CORE::getInstance('Router');
$apiRouterModule->setRouter($apiRoutes);
