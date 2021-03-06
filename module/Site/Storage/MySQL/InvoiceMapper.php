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
     * Updates invoice status by its token
     * 
     * @param string $token
     * @param int $status
     * @return boolean
     */
    public function updateStatusByToken(string $token, int $status) : bool
    {
        return $this->db->update(self::getTableName(), ['status' => $status])
                        ->whereEquals('token', $token)
                        ->execute();
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
     * @param int $page Current page number
     * @param int $perPageCount Per page count
     * @return array
     */
    public function fetchAll(int $page, int $perPageCount) : array
    {
        return $this->db->select('*')
                        ->from(self::getTableName())
                        ->orderBy($this->getPk())
                        ->desc()
                        ->paginate($page, $perPageCount)
                        ->queryAll();
    }
}
