<?php

return array(
    '/auth/:action/(\w+)/(oauth1|oauth2)*' =>  array(
        'module' => 'EvaOAuthClient',
        'controller' => 'auth',
        'action' => 1,
        'service' => 2,
        'auth' => 3,
    ),

    '/auth/:action' =>  array(
        'module' => 'EvaOAuthClient',
        'controller' => 'auth',
        'action' => 1,
    ),
);
