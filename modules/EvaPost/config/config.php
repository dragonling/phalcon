<?php

return array(
    'routes' =>  array(
        '/admin/post' =>  array(
            'module' => 'EvaPost',
            'controller' => 'post',
        ),
        '/admin/post/:action' =>  array(
            'module' => 'EvaPost',
            'controller' => 'post',
            'action' => 1,
        ),
    ),
);

