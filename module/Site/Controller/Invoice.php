<?php

namespace Site\Controller;

use Krystal\Validate\Pattern;

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
                        'product' => new Pattern\Name(),
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
