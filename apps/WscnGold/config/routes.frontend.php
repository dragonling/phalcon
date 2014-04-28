<?php

return array(
    '/' =>  array(
        'module' => 'WscnGold',
        'controller' => 'index',
        'action' => 'index'
    ),    
    '/news' =>  array(
        'module' => 'WscnGold',
        'controller' => 'news',
    ), 
    '/news/:action(/(\d+))*' =>  array(
        'module' => 'WscnGold',
        'controller' => 'news',
        'action' => 1,
        'id' => 3,
    ),
    '/post/(\w+)' =>  array(
        'module' => 'WscnGold',
        'controller' => 'post',
        'action' => 'article',
        'id' => 1,
    ),
    '/livenews' =>  array(
        'module' => 'WscnGold',
        'controller' => 'livenews',
    ),
    '/calendar' =>  array(
        'module' => 'WscnGold',
        'controller' => 'calendar',
    ),
    '/tutorial' =>  array(
        'module' => 'WscnGold',
        'controller' => 'index',
        'action' => 'tutorial' 
    ), 
    '/gold/:action' =>  array(
        'module' => 'WscnGold',
        'controller' => 'index',
        'action' => 1 
    ), 
    '/techanalysis' =>  array(
        'module' => 'WscnGold',
        'controller' => 'techanalysis',
    ), 
    '/techanalysis/:action(/(\d+))*' =>  array(
        'module' => 'WscnGold',
        'controller' => 'techanalysis',
        'action' => 1,
        'id' => 3,
    ),
);

