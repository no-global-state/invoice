<?php

namespace Site\Controller;

final class Invoice extends AbstractSiteController
{
    /**
     * Render all invoices
     * 
     * @return string
     */
    public function indexAction()
    {
        $invoiceService = $this->getModuleService('invoiceService');

        return $this->view->render('invoice/index', [
            'invoices' => $invoiceService->fetchAll()
        ]);
    }
}
