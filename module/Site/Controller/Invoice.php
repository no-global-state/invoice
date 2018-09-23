<?php

namespace Site\Controller;

use Krystal\Validate\Pattern;
use Krystal\Stdlib\VirtualEntity;
use Site\Service\MailerService;
use Site\Gateway\GatewayService;

final class Invoice extends AbstractSiteController
{
    /**
     * {@inheritDoc}
     */
    protected function bootstrap(string $action)
    {
        // Disabled CSRF for gateway action
        if ($action === 'successAction') {
            $this->enableCsrf = false;
        }

        parent::bootstrap($action);
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
            $backUrl = $this->request->getBaseUrl() . $this->createUrl('Site:Invoice@successAction', [$token]);
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
            // If not logged in, then reset language to default
            if (!$this->getAuthService()->isLoggedIn()) {
                $this->loadTranslations('en');
            }

            $entity = new VirtualEntity();

            // Fill amount and product if provided
            $entity['product'] = $this->request->getQuery('product');
            $entity['amount'] = $this->request->getQuery('amount', false);

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
                // Create email body
                $body = $this->view->renderRaw('Site', 'mail', 'new', $data);

                // Now send it
                MailerService::send($_ENV['adminEmail'], $this->translator->translate('Your have a new invoice'), $body);

                // Add now and get last token
                $token = $this->getModuleService('invoiceService')->add($data);

                // If amount not provided, then update
                if (!isset($data['amount'])) {
                    $this->flashBag->set('success', 'Thanks! Your invoice has been sent');
                    return '1';
                } else {
                    // Otherwise redirect to payment page
                    return $this->json([
                        'url' => $this->request->getBaseUrl() . $this->createUrl('Site:Invoice@gatewayAction', [$token])
                    ]);
                }

            } else {
                return $formValidator->getErrors();
            }
        }
    }
}
