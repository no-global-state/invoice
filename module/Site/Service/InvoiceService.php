<?php

namespace Site\Service;

use Site\Storage\MySQL\InvoiceMapper;
use Krystal\Text\TextUtils;
use Krystal\Date\TimeHelper;

final class InvoiceService
{
    /**
     * Any compliant invoice mapper
     * 
     * @var \Site\Storage\MySQL\InvoiceMapper
     */
    private $invoiceMapper;

    /**
     * State initialization
     * 
     * @param \Site\Storage\MySQL\InvoiceMapper $invoiceMapper
     * @return void
     */
    public function __construct(InvoiceMapper $invoiceMapper)
    {
        $this->invoiceMapper = $invoiceMapper;
    }

    /**
     * Returns pagination instance
     * 
     * @return \Krystal\Paginate\Paginator
     */
    public function getPaginator()
    {
        return $this->invoiceMapper->getPaginator();
    }

    /**
     * Deletes invoice by its ID
     * 
     * @param int $id Invoice ID
     * @return mixed
     */
    public function deleteById(int $id)
    {
        return $this->invoiceMapper->deleteByPk($id);
    }

    /**
     * Updates invoice
     * 
     * @param array $input
     * @return boolean
     */
    public function update(array $input) : bool
    {
        return $this->invoiceMapper->persist($input);
    }

    /**
     * Adds new invoice
     * 
     * @param array $input
     * @return boolean
     */
    public function add(array $input) : bool
    {
        return $this->invoiceMapper->persist([
            'product' => $input['product'],
            'amount' => $input['amount'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'client' => $input['client'],
            'status' => -1,
            'token' => TextUtils::uniqueString(),
            'datetime' => TimeHelper::getNow()
        ]);
    }

    /**
     * Confirms that payment is done by token
     * 
     * @param string $token
     * @return boolean
     */
    public function confirmPayment(string $token) : bool
    {
        return $this->invoiceMapper->updateStatusByToken($token, 1);
    }

    /**
     * Finds row by its associated token
     * 
     * @param string $token
     * @return array
     */
    public function findByToken(string $token)
    {
        return $this->invoiceMapper->findByToken($token);
    }

    /**
     * Fetch all invoices
     * 
     * @param int $page Current page number
     * @param int $perPageCount Per page count
     * @return array
     */
    public function fetchAll(int $page, int $perPageCount) : array
    {
        return $this->invoiceMapper->fetchAll($page, $perPageCount);
    }
}
