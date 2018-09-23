<?php

namespace Site\Controller;

use Krystal\Validate\Pattern;
use Krystal\Stdlib\VirtualEntity;
use Site\Service\MailerService;
use Site\Gateway\GatewayService;

final class Invoice extends AbstractSiteController
{
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
     * Creates new invoice
     * 
     * @return string
     */
    public function newAction()
    {
        if ($this->request->isGet()) {
            $entity = new VirtualEntity();

            // Fill amount and product if provided
            $entity['product'] = $this->request->getQuery('product');
            $entity['amount'] = $this->request->getQuery('amount');

            return $this->view->render('invoice/form', [
                'invoice' => $entity,
                'title' => 'New invoice'
            ]);

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

                // Create email body
                $body = $this->view->renderRaw('Site', 'mail', 'new', $data);

                // Now send it
                MailerService::send($_ENV['adminEmail'], 'Your have new invoice', $body);

                $this->flashBag->set('success', 'Thanks! Your invoice has been sent');
                return '1';
            } else {
                return $formValidator->getErrors();
            }
        }
    }
}
