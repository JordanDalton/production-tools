<?php
/**
 * Class for ITBAL table on the IBM i
 *
 * @category WrsGroup
 * @package Model
 * @subpackage DbTable
 * @author Eugene Morgan
 */
class WrsGroup_Model_DbTable_APlus_Itbal
    extends WrsGroup_Db_Table_OdbcDb2_Abstract
{
	protected $_name = 'itbal';
    protected $_primary = array('ibitno', 'ibwhid');

    /**
     * Gets quantity on hand, other basic "item balance" info about one or
     * more items
     *
     * @param string|array $itemNumbers One or more item numbers to query
     * @return Zend_Db_Table_Rowset_Abstract A rowset of data
     */
    public function getBasicInfo($itemNumbers)
    {
        $select = $this->select()
            ->from($this, array(
                'TRIM(ibitno) as itno',
                'ibohq1 AS qty_on_hand',
                'ibpoq1 AS qty_on_po',
            ));
        if (is_array($itemNumbers)) {
            $select->where('ibitno IN (?)', $itemNumbers);
        } else {
            $select->where('ibitno = ?', $itemNumbers);
        }
        return $this->fetchAll($select);
    }

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
                'TRIM(ibinus) AS ibinus',
                'TRIM(ibwmiu) AS ibwmiu'
            ))
            ->where('ibitno = ?', $itemNumber);
        $rowset = $this->fetchAll($select);
        foreach ($rowset as $row) {
            if ($row->ibinus || $row->ibwmiu) {
                return true;
            }
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
                'ibwmiu' => '',
                'ibinus' => ''
            ),
            $this->getAdapter()->quoteInto('ibitno = ?', $itemNumber)
        );
    }
}