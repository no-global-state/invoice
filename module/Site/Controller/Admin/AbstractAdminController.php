<?php

namespace Site\Controller\Admin;

use Krystal\Application\Controller\AbstractAuthAwareController;
use Krystal\Validate\Renderer;

abstract class AbstractAdminController extends AbstractAuthAwareController
{
    /**
     * {@inheritDoc}
     */
    protected function getAuthService()
    {
        return $this->getModuleService('userService');
    }

    /**
     * {@inheritDoc}
     */
    protected function onSuccess()
    {
        //$this->response->redirect($this->createUrl('Site:Admin:Invoice@indexAction'));
    }

    /**
     * {@inheritDoc}
     */
    protected function onFailure()
    {
        $this->response->redirect($this->createUrl('Site:Auth@indexAction'));
    }

    /**
     * {@inheritDoc}
     */
    protected function onNoRights()
    {
        die($this->translator->translate('You do not have enough rights to perform this action!'));
    }
    
    /**
     * This method automatically gets called when this controller executes
     * 
     * @return void
     */
    protected function bootstrap()
    {
        // Define the default renderer for validation error messages
        $this->validatorFactory->setRenderer(new Renderer\StandardJson());

        // Define a directory where partial template fragments must be stored
        $this->view->getPartialBag()
                   ->addPartialDir($this->view->createThemePath('Site', $this->appConfig->getTheme()).'/partials/');

        // Append required assets
        $this->view->getPluginBag()->appendStylesheets(array(
            '@Site/bootstrap/css/bootstrap.min.css',
            '@Site/styles.css'
        ));

        // Append required script paths
        $this->view->getPluginBag()->appendScripts(array(
            '@Site/jquery.min.js',
            '@Site/bootstrap/js/bootstrap.min.js',
            '@Site/krystal.jquery.js'
        ));

        // Add shared variables
        $this->view->addVariables(array(
            'isLoggedIn' => $this->getAuthService()->isLoggedIn()
        ));

        // Define the main layout
        $this->view->setLayout('__layout__');
    }
}
