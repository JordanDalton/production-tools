<?php
/**
 * Class for WMBAL table on the IBM i
 *
 * @category WrsGroup
 * @package Model
 * @subpackage DbTable
 * @author Eugene Morgan
 */
class WrsGroup_Model_DbTable_APlus_Wmbal
    extends WrsGroup_Db_Table_OdbcDb2_Abstract
{
	protected $_name = 'wmbal';
    protected $_primary = array(
        'wbitno',
        'wbwhid',
        'wbassq',
        'wbloca',
        'wbltsr',
        'wbcofo'
    );

    /**
     * Gets total quantity in one or more locations for one or more item
     * numbers
     *
     * @param string|array $itemNumbers One or more item numbers to query
     * @param string|array $locations One or more locations to query
     * @return Zend_Db_Table_Rowset_Abstract A rowset of data
     */
    public function getTotalQtyInLocations($itemNumbers, $locations)
    {
        $select = $this->select()
            ->from($this, array(
                'TRIM(wbitno) AS itno',
                'SUM(wbohq1) AS qty'
            ));
        if (is_array($itemNumbers)) {
            $select->where('wbitno IN (?)', $itemNumbers);
        } else {
            $select->where('wbitno = ?', $itemNumbers);
        }
        if (is_array($locations)) {
            $select->where('wbloca IN (?)', $locations);
        } else {
            $select->where('wbloca = ?', $locations);
        }
        $select->group('wbitno');
        return $this->fetchAll($select);
    }
}