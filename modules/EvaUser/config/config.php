<?php
return array(
    'user' => array(
        'registerUri' => '/admin/login',
        'registerSuccessRedirectUri' => '/admin/login',
        'registerFailedRedirectUri' => '/admin/login',
        'loginUri' => '/admin',
        'loginSuccessRedirectUri' => '/admin/dashboard',
        'loginFailedRedirectUri' => '/admin/login',
        'activeMailTemplate' => __DIR__ . '/../views/mails/active.phtml',
        'activeSuccessRedirectUri' => '/admin/login',
        'activeFailedRedirectUri' => '/admin/login',
        'resetSuccessRedirectUri' => '/admin/login',
        'resetFailedRedirectUri' => '/admin/login',
        'resetMailTemplate' => __DIR__ . '/../views/mails/reset.phtml',
        'cookieTokenExpired' => 500000,
    ),   

    'routes' => array(
        '/admin/register' => array(
            'module' => 'EvaUser',
            'controller' => 'Admin\Register',
        ),

        '/admin/register/:action' => array(
            'module' => 'EvaUser',
            'controller' => 'Admin\Register',
            'action' => 1, 
        ),

        '/admin/login' => array(
            'module' => 'EvaUser',
            'controller' => 'Admin\Login',
        ),

        '/admin/login/:action([\w/]*)' => array(
            'module' => 'EvaUser',
            'controller' => 'Admin\Login',
            'action' => 1, 
        ),

        '/admin/logout' => array(
            'module' => 'EvaUser',
            'controller' => 'Admin\Logout',
        ),

        '/admin/session/verify/(\w+)/(\w+)' => array(
            'module' => 'EvaUser',
            'controller' => 'Admin\Session',
            'action' => 'verify',
            'username' => 1,
            'code' => 2,
        ),

        '/admin/session/reset/(\w+)/(\w+)' => array(
            'module' => 'EvaUser',
            'controller' => 'Admin\Session',
            'action' => 'reset',
            'username' => 1,
            'code' => 2,
        ),

        '/admin/session/:action' => array(
            'module' => 'EvaUser',
            'controller' => 'Admin\Session',
            'action' => 1, 
        ),
    ),

);
