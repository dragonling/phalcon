<?php

/*
  +------------------------------------------------------------------------+
  | Phosphorum                                                             |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2014 Phalcon Team and contributors                  |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file docs/LICENSE.txt.                        |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
*/

$router = new Phalcon\Mvc\Router(false);

//$router->setDefaultModule("frontend");
#$router->setDefaultNamespace("Eva\Frontend\Controllers");


$router->add('/user', array(
    'module' => 'evauser',
    'controller' => 'user',
    'action' => 'index'
));

$router->add('/', array(
    'module' => 'frontend',
    'controller' => 'index',
    'action' => 'index'
));

$router->add('/post', array(
    'module' => 'evapost',
    'controller' => 'user',
    'action' => 'index'
));
return $router;
