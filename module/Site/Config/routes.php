<?php

return [
    '/site/captcha/(:var)' => [
        'controller' => 'Site@captchaAction'
    ],

    '/' => [
        'controller' => 'Site@indexAction'
    ],

    '/hello/(:var)' => [
        'controller' => 'Site@helloAction',
    ],

    '/contact' => [
        'controller' => 'Contact@indexAction'
    ],
    
    '/auth/login' => [
        'controller' => 'Auth@indexAction'
    ],
    
    '/auth/logout' => [
        'controller' => 'Auth@logoutAction'
    ],
    
    '/register' => [
        'controller' => 'Register@indexAction'
    ],
    
    '/invoices' => [
        'controller' => 'Admin:Invoice@indexAction'
    ],
    
    '/invoices/new' => [
        'controller' => 'Invoice@newAction'
    ],
    
    '/invoices/gateway/(:var)' => [
        'controller' => 'Invoice@gatewayAction'
    ],
    
    '/invoices/success/(:var)' => [
        'controller' => 'Invoice@successAction'
    ],
    
    '/invoices/notify/(:var)' => [
        'controller' => 'Admin:Invoice@notifyAction'
    ],
    
    '/invoices/edit/(:var)' => [
        'controller' => 'Admin:Invoice@editAction'
    ],
    
    '/invoices/delete/(:var)' => [
        'controller' => 'Admin:Invoice@deleteAction'
    ]
];
