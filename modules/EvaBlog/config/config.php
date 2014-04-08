<?php

return array(
    'routes' =>  array(
        '/admin/category' =>  array(
            'module' => 'EvaBlog',
            'controller' => 'category',
        ),
        '/admin/category/:action' =>  array(
            'module' => 'EvaBlog',
            'controller' => 'category',
            'action' => 1,
        ),
        '/admin/post' =>  array(
            'module' => 'EvaBlog',
            'controller' => 'post',
        ),
        '/admin/post/:action' =>  array(
            'module' => 'EvaBlog',
            'controller' => 'post',
            'action' => 1,
        ),
    ),
);

