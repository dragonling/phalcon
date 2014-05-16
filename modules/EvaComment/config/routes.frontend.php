<?php

return array(
    '/thread' =>  array(
        'module' => 'EvaComment',
        'controller' => 'thread',
    ),


    '/thread/(\w+)/comments' => array(
        'module' => 'EvaComment',
        'controller' => 'thread',
        'action' => 'getThreadComments',
        'uniqueKey' => 1,
    ),

    '/thread/(\w+)/comments/new' => array(
        'module' => 'EvaComment',
        'controller' => 'thread',
        'action' => 'postThreadComments',
        'threadKey' => 1,
    ),

    '/thread/:action' =>  array(
        'module' => 'EvaComment',
        'controller' => 'thread',
        'action' => 1,
    ),
);

