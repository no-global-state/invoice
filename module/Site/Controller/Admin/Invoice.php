<?php

namespace Site\Controller\Admin;

use Site\Service\MailerService;
use Site\Controller\AbstractSiteController;

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

    /**
     * Deletes invoice by its ID
     * 
     * @param int $id Invoice ID
     * @return mixed
     */
    public function deleteAction(int $id)
    {
        $this->getModuleService('invoiceService')->deleteById($id);

        $this->flashBag->set('success', 'Selected invoice has been successfully deleted');
        $this->response->redirectToPreviousPage();
    }

    /**
     * Renders edit form
     * 
     * @param string $token
     * @return mixed
     */
    public function editAction(string $token)
    {
        // Grab the service
        $invoiceService = $this->getModuleService('invoiceService');

        if ($this->request->isGet()) {
            $invoice = $invoiceService->findByToken($token);

            if ($invoice) {
                return $this->view->render('invoice/form', [
                    'invoice' => $invoice
                ]);
            } else {
                // Invalid token provided
                return false;
            }
        } else {
            // Update request
            $invoiceService->update($this->request->getPost());

            $this->flashBag->set('success', 'Invoice has been updated successfully');
            return 1;
        }
    }

    /**
     * Notify client about payment
     * 
     * @param string $token
     * @return mixed
     */
    public function notifyAction(string $token)
    {
        // Find invoice by its token
        $invoice = $this->getModuleService('invoiceService')->findByToken($token);

        if ($invoice) {
            $params = array_merge($invoice, [
                'link' => $this->request->getBaseUrl() . $this->createUrl('Site:Invoice@gatewayAction', [$invoice['token']])
            ]);

            // Create email body
            $body = $this->view->renderRaw('Site', 'mail', 'notify', $params);

            // Now send it
            MailerService::send($invoice['email'], 'Please confirm payment', $body);

            $this->flashBag->set('success', sprintf('Notification to %s has been successfully sent', $invoice['email']));
            $this->response->redirectToPreviousPage();

        } else {
            // Invalid token
            return false;
        }
    }
}