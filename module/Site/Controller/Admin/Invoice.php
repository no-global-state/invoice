<?php

namespace Site\Controller\Admin;

use Site\Service\MailerService;

final class Invoice extends AbstractAdminController
{
    /**
     * Render all invoices
     * 
     * @param int $page Current page number
     * @return string
     */
    public function indexAction($page)
    {
        $perPageCount = 15;

        if (!$page) {
            $page = 1;
        }

        $invoiceService = $this->getModuleService('invoiceService');

        // Configure pagination instance
        $paginator = $invoiceService->getPaginator();
        $paginator->setUrl($this->createUrl('Site:Admin:Invoice@indexAction'));

        return $this->view->render('invoice/index', [
            'invoices' => $invoiceService->fetchAll($page, $perPageCount),
            'paginator' => $paginator,
            'currency' => '$'
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
                // Shared title
                $title = 'Edit invoice';

                // Append breadcrumb
                $this->view->getBreadcrumbBag()->addOne('Invoices', $this->createUrl('Site:Admin:Invoice@indexAction', [1]))
                                               ->addOne($title);

                return $this->view->render('invoice/form', [
                    'invoice' => $invoice,
                    'title' => $title
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

            $this->flashBag->set('success', $this->translator->translate('Notification to %s has been successfully sent', $invoice['email']));
            $this->response->redirectToPreviousPage();

        } else {
            // Invalid token
            return false;
        }
    }
}
