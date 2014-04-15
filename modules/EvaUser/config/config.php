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
);
