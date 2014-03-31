<?php

return array(
    'routes' =>  array(
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

