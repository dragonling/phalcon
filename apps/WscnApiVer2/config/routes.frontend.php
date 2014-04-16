<?php

return array(
    '/' =>  array(
        'module' => 'WscnApiVer2',
        'controller' => 'index',
        'action' => 'index'
    ),
    '/v2/resources' =>  array(
        'module' => 'WscnApiVer2',
        'controller' => 'index',
        'action' => 'resources',
    ),
    '/v2/resource/(\w+)' =>  array(
        'module' => 'WscnApiVer2',
        'controller' => 'index',
        'action' => 'details',
        'id' => 1,
    ),
    '/v2/:controller/:action(/(\w+))*(\.(json|jsonp|xml))*' =>  array(
        'module' => 'WscnApiVer2',
        'controller' => 1,
        'action' => 2,
        'id' => 4,
        'format' => 6,
    ),
);

