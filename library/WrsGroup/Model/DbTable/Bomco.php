<?php
/**
 * Class for BOMCO table on the IBM i
 *
 * @category WrsGroup
 * @package Model
 * @subpackage DbTable
 * @author Eugene Morgan
 */
class WrsGroup_Model_DbTable_Bomco extends WrsGroup_Db_Table_OdbcDb2_Abstract
{
	protected $_name = 'BOMCO';
    protected $_primary = array(
        'BCCONO',
        'BCCSNO',
        'BCORNO',
        'BCORSQ',
        'BCPRIT',
        'BCBMSQ'
    );

    /**
     * Gets all parents with the given item(s) as components
     *
     * @param string|array $itemNumbers One or more item numbers to query
     * @return Zend_Db_Table_Rowset_Abstract A rowset of data
     */
    public function getParentItems($itemNumbers)
    {
        $select = $this->select()
            ->from($this, array(
                'TRIM(BCCMIT) AS item_number',
                'TRIM(BCPRIT) AS parent_item',
                'BCQTPR AS quantity_of_child',
            ));
        if (is_array($itemNumbers)) {
            $select->where('BCCMIT IN (?)', $itemNumbers);
        } else {
            $select->where('BCCMIT = ?', $itemNumbers);
        }
        return $this->fetchAll($select);
    }
}