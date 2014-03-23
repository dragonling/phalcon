<?php
return array(
    'user' => array(
        'registerUri' => '/admin',
        'registerSuccessRedirectUri' => '/admin',
        'registerFailedRedirectUri' => '/admin',
        'loginUri' => '/admin',
        'loginSuccessRedirectUri' => '/admin/dashboard',
        'loginFailedRedirectUri' => '/admin',
        'activeMailTemplate' => __DIR__ . '/../views/mails/active.phtml',
        'activeSuccessRedirectUri' => '/admin',
        'activeFailedRedirectUri' => '/admin',
        'resetSuccessRedirectUri' => '/admin',
        'resetFailedRedirectUri' => '/admin',
        'resetMailTemplate' => __DIR__ . '/../views/mails/reset.phtml',
        'cookieTokenExpired' => 500000,
    ),   

    'routes' => array(
        '/register' => array(
            'module' => 'EvaUser',
            'controller' => 'register',
        ),

        '/register/:action' => array(
            'module' => 'EvaUser',
            'controller' => 'register',
            'action' => 1, 
        ),

        '/login' => array(
            'module' => 'EvaUser',
            'controller' => 'login',
        ),

        '/login/:action([\w/]*)' => array(
            'module' => 'EvaUser',
            'controller' => 'login',
            'action' => 1, 
        ),

        '/logout' => array(
            'module' => 'EvaUser',
            'controller' => 'logout',
        ),

        '/session/verify/(\w+)/(\w+)' => array(
            'module' => 'EvaUser',
            'controller' => 'session',
            'action' => 'verify',
            'username' => 1,
            'code' => 2,
        ),

        '/session/reset/(\w+)/(\w+)' => array(
            'module' => 'EvaUser',
            'controller' => 'session',
            'action' => 'reset',
            'username' => 1,
            'code' => 2,
        ),

        '/session/:action' => array(
            'module' => 'EvaUser',
            'controller' => 'session',
            'action' => 1, 
        ),

        /*
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
        */
    ),

);
