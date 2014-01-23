<?php
/**
 * Class for LG shipments items table
 *
 * @category WrsGroup
 * @package Model
 * @subpackage DbTable
 * @author Eugene Morgan
 */
class WrsGroup_Model_DbTable_LgItems_LgShipmentsItems
    extends WrsGroup_Db_Table_OdbcDb2_Abstract
{
	protected $_name = 'lg_shipments_items';
	protected $_schema = 'lgitems';
    protected $_primary = array('shipment_id', 'shipment_itno');

    /**
     * @var Zend_Config
     */
    protected $_config;

    /**
     * Sets the config object
     *
     * @param Zend_Config $config The config object
     */
    public function setConfig($config)
    {
        $this->_config = $config;
    }

    /**
     * Gets pending LG shipment items
     *
     * @return Zend_Db_Table_Row_Abstract A rowset of data
     */
    public function getPendingLgShipmentItems()
    {
        $select = $this->select()
            ->from(
                array('lsi' => 'lg_shipments_items'),
                array(
                    new Zend_Db_Expr('shipment_itno AS fabric_itno'),
                    'qty_shipped'
                )
            )
            ->setIntegrityCheck(false)
            ->joinInner(
                array('ls' => 'lg_shipments'),
                'ls.shipment_id = lsi.shipment_id',
                array('po_numbers', 'description', 'arrival_date')
            )
            ->where('ls.confirmed = ?', 'N');
        return $this->fetchAll($select);
    }
}
