<?php

return array(
    '/' =>  array(
        'module' => 'WscnApiVer2',
        'controller' => 'index',
        'action' => 'index'
    ),
    '/v2/resources/(\w+)' =>  array(
        'module' => 'WscnApiVer2',
        'controller' => 'index',
        'action' => 'resource',
        'id' => 1,
    ),
    '/v2/resources' =>  array(
        'module' => 'WscnApiVer2',
        'controller' => 'index',
        'action' => 'resources',
    ),
    '/v2/post' =>  array(
        'module' => 'WscnApiVer2',
        'controller' => 'post',
        'action' => 'index',
    ),
    '/v2/post/(\d+)' =>  array(
        'module' => 'WscnApiVer2',
        'controller' => 'post',
        'action' => 'get',
        'id' => 1,
    ),
    /*
    '/v2/:controller/:action(/(\w+))*(\.(json|jsonp|xml))*' =>  array(
        'module' => 'WscnApiVer2',
        'controller' => 1,
        'action' => 2,
        'id' => 4,
        'format' => 6,
    ),
    */
);

