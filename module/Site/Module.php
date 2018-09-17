<?php

namespace Site;

use Krystal\Application\Module\AbstractModule;
use Site\Service\UserService;
use Site\Storage\Memory\UserMapper;
use Site\Service\InvoiceService;

final class Module extends AbstractModule
{
    /**
     * Returns routes of this module
     * 
     * @return array
     */
    public function getRoutes()
    {
        return include(__DIR__ . '/Config/routes.php');
    }

    /**
     * Returns prepared service instances of this module
     * 
     * @return array
     */
    public function getServiceProviders()
    {
        $authManager = $this->getServiceLocator()->get('authManager');

        $userService = new UserService($authManager, new UserMapper());
        $authManager->setAuthService($userService);

        return array(
            'userService' => $userService,
            'invoiceService' => new InvoiceService($this->createMapper('\Site\Storage\MySQL\InvoiceMapper'))
        );
    }
}
