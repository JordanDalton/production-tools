<?php
/**
 * Class for POHED table on the IBM i
 *
 * @category WrsGroup
 * @package Model
 * @subpackage DbTable
 * @author Eugene Morgan
 */
class WrsGroup_Model_DbTable_APlus_Pohed
    extends WrsGroup_Db_Table_OdbcDb2_Abstract
{
	protected $_name = 'pohed';
    protected $_primary = array('phcono', 'phorid');

    /**
     * Tells whether a purchase order is locked
     *
     * @param string $poNumber The PO number
     * @return boolean Whether the PO is locked in this table
     */
    public function getLockStatus($poNumber)
    {
        $select = $this->select()
            ->from($this, array(
                'TRIM(phinus) AS phinus'
            ))
            ->where('phorid = ?', $poNumber);
        $row = $this->fetchRow($select);
        if ($row->phinus) {
            return true;
        }
        return false;
    }

    /**
     * Unlocks a PO
     *
     * @param string $poNumber The PO number
     */
    public function unlockPo($poNumber)
    {
        $this->update(
            array(
                'phinus' => ''
            ),
            $this->getAdapter()->quoteInto('phorid = ?', $poNumber)
        );
    }
}