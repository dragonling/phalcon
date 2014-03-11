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

$router = new Phalcon\Mvc\Router();

$router->setDefaultModule("frontend");
#$router->setDefaultNamespace("Eva\Frontend\Controllers");

$router->add('/user', array(
    'module' => 'user',
    'controller' => '\Eva\EvaUser\Controllers\User',
    'action' => 'index'
));

return $router;
