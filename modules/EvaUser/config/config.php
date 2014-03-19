<?php
return array(
    'user' => array(
        'loginUri' => '/',
        'loginSuccessRedirectUri' => '/',
        'loginFailedRedirectUri' => '/',
        'cookieTokenExpired' => 500000,
    ),   

    'routes' => array(
        '/admin/user' => array(
            'module' => 'EvaUser',
            'controller' => 'Admin\User',
            'action' => 'index'
        ),

        '/user' => array(
            'module' => 'EvaUser',
            'controller' => 'user',
            'action' => 'index'
        ),

        '/user/verify/(\d+)/(\w+)' => array(
            'module' => 'EvaUser',
            'controller' => 'user',
            'action' => 'verify',
            'userId' => 1,
            'code' => 2,
        ),

        '/user/reset/(\w+)/(\w+)' => array(
            'module' => 'EvaUser',
            'controller' => 'user',
            'action' => 'reset',
            'username' => 1,
            'code' => 2,
        ),

        '/user/:action' => array(
            'module' => 'EvaUser',
            'controller' => 'user',
            'action' => 1,
        )
    ),

);
