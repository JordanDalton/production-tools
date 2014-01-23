<?php
/**
 * Class for ITMST table on the IBM i
 *
 * @category WrsGroup
 * @package Model
 * @subpackage DbTable
 * @author Eugene Morgan
 */
class WrsGroup_Model_DbTable_APlus_Itmst
    extends WrsGroup_Db_Table_OdbcDb2_Abstract
{
	protected $_name = 'itmst';
    protected $_primary = array('imitno');

    /**
     * Tells whether an item is locked
     *
     * @param string $itemNumber The item number
     * @return boolean Whether the item is locked in this table
     */
    public function getLockStatus($itemNumber)
    {
        $select = $this->select()
            ->from($this, array(
                'TRIM(iminus) AS iminus'
            ))
            ->where('imitno = ?', $itemNumber);
        $row = $this->fetchRow($select);
        if ($row->iminus) {
            return true;
        }
        return false;
    }

    /**
     * Unlocks an item
     *
     * @param string $itemNumber The item number
     */
    public function unlockItem($itemNumber)
    {
        $this->update(
            array(
                'iminus' => ''
            ),
            $this->getAdapter()->quoteInto('imitno = ?', $itemNumber)
        );
    }
}