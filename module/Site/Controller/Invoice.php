<?php

namespace Site\Controller;

use Krystal\Validate\Pattern;
use Site\Gateway\GatewayService;
use Site\Service\MailerService;

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
     * Handle success or failure after payment gets done
     * 
     * @param string $token
     * @return mixed
     */
    public function successAction(string $token)
    {
        // Make sure they didn't press Cancel button
        if (GatewayService::transactionFailed()) {
            return $this->view->render('invoice/cancel');
        }

        $success = $this->getModuleService('invoiceService')->confirmPayment($token);

        if ($success) {
            return $this->view->render('invoice/success');
        }
    }

    /**
     * Invokes gateway by associated token
     * 
     * @param string $token
     * @return mixed
     */
    public function gatewayAction(string $token)
    {
        // Find invoice by its token
        $invoice = $this->getModuleService('invoiceService')->findByToken($token);

        if ($invoice) {
            // Create back URL
            $backUrl = $this->createUrl('Site:Invoice@successAction', [$token]);
            $gateway = GatewayService::factory($invoice['id'], $invoice['amount'], $backUrl);

            return $this->view->disableLayout()->render('gateway', [
                'gateway' => $gateway
            ]);

        } else {
            // Invalid token
            return false;
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

    /**
     * Creates new invoice
     * 
     * @return string
     */
    public function newAction()
    {
        if ($this->request->isGet()) {
            return $this->view->render('invoice/form');

        } else {
            $data = $this->request->getPost();

            // Build form validator
            $formValidator = $this->createValidator([
                'input' => [
                    'source' => $data,
                    'definition' => [
                        'client' => new Pattern\Name(),
                        'product' => new Pattern\Name(),
                        'email' => new Pattern\Email(),
                        'phone' => new Pattern\Phone(),
                        'captcha' => new Pattern\Captcha($this->captcha)
                    ]
                ]
            ]);

            if ($formValidator->isValid()) {
                // Add now
                $this->getModuleService('invoiceService')->add($data);

                $this->flashBag->set('success', 'Thanks! Your invoice has been sent');
                return '1';
            } else {
                return $formValidator->getErrors();
            }
        }
    }
}
