<?php

return [
    '/site/captcha/(:var)' => [
        'controller' => 'Site@captchaAction'
    ],

    '/' => [
        'controller' => 'Site@indexAction'
    ],

    '/auth/login' => [
        'controller' => 'Auth@indexAction'
    ],
    
    '/auth/logout' => [
        'controller' => 'Auth@logoutAction'
    ],
    
    '/invoices/new/(:var)' => [
        'controller' => 'Invoice@newAction'
    ],
    
    '/invoices/gateway/(:var)' => [
        'controller' => 'Invoice@gatewayAction'
    ],
    
    '/invoices/success/(:var)' => [
        'controller' => 'Invoice@successAction'
    ],
    
    '/admin/invoices/(:var)' => [
        'controller' => 'Admin:Invoice@indexAction'
    ],
    
    '/admin/invoices/notify/(:var)' => [
        'controller' => 'Admin:Invoice@notifyAction'
    ],
    
    '/admin/invoices/edit/(:var)' => [
        'controller' => 'Admin:Invoice@editAction'
    ],
    
    '/admin/invoices/delete/(:var)' => [
        'controller' => 'Admin:Invoice@deleteAction'
    ]
];
