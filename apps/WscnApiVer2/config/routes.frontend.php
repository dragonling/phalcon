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
    'postlist' =>  array(
        'pattern' => '/v2/post',
        'paths' => array(
            'module' => 'WscnApiVer2',
            'controller' => 'post',
            'action' => 'index',
        ),
        'httpMethods' => 'GET'
    ),
    'createpost' =>  array(
        'pattern' => '/v2/post',
        'paths' => array(
            'module' => 'WscnApiVer2',
            'controller' => 'post',
            'action' => 'post',
        ),
        'httpMethods' => 'POST'
    ),
    'getpost' =>  array(
        'pattern' => '/v2/post/(\d+)',
        'paths' => array(
            'module' => 'WscnApiVer2',
            'controller' => 'post',
            'action' => 'get',
            'id' => 1,
        ),
        'httpMethods' => 'GET'
    ),
    'putpost' =>  array(
        'pattern' => '/v2/post/(\d+)',
        'paths' => array(
            'module' => 'WscnApiVer2',
            'controller' => 'post',
            'action' => 'put',
            'id' => 1,
        ),
        'httpMethods' => 'PUT'
    ),
    'deletepost' =>  array(
        'pattern' => '/v2/post/(\d+)',
        'paths' => array(
            'module' => 'WscnApiVer2',
            'controller' => 'post',
            'action' => 'delete',
            'id' => 1,
        ),
        'httpMethods' => 'DELETE'
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

