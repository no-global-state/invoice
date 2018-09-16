<?php

namespace Site\Storage\MySQL;

use Krystal\Db\Sql\AbstractMapper;

final class InvoiceMapper extends AbstractMapper
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return 'invoices';
    }

    /**
     * {@inheritDoc}
     */
    protected function getPk()
    {
        return 'id';
    }

    /**
     * Finds row by its associated token
     * 
     * @param string $token
     * @return array
     */
    public function findByToken(string $token)
    {
        return $this->fetchByColumn('token', $token);
    }

    /**
     * Fetch all invoices
     * 
     * @return array
     */
    public function fetchAll() : array
    {
        return $this->db->select('*')
                        ->from(self::getTableName())
                        ->orderBy($this->getPk())
                        ->queryAll();
    }
}
