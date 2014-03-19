<?php

return array(
    'routes' => array(
        '/admin' => array(
            'module' => 'EvaCore',
            'controller' => 'Admin\Index',
            'action' => 'index'
        ),
        '/admin/dashboard' => array(
            'module' => 'EvaCore',
            'controller' => 'Admin\Index',
            'action' => 'dashboard'
        ),
    ),
);
