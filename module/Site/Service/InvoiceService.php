<?php

namespace Site\Service;

use Site\Storage\MySQL\InvoiceMapper;

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
     * Fetch all invoices
     * 
     * @return array
     */
    public function fetchAll() : array
    {
        return $this->invoiceMapper->fetchAll();
    }
}
