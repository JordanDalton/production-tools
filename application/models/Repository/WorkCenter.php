<?php
/**
 * Repository class for work centers
 *
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
class Application_Model_Repository_WorkCenter
{
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * @var string The default warehouse ID
     */
    protected $_defaultWarehouseId;

    /**
     * Constructor
     * 
     * @param Zend_Db_Adapter_Abstract $db An instance of the db adapter
     */
    public function __construct($db)
    {
        $this->_db = $db;
    }

    /**
     * Sets the default warehouse ID
     * 
     * @param string $warehouseId The warehouse ID
     */
    public function setDefaultWarehouseId($warehouseId)
    {
        $this->_defaultWarehouseId = $warehouseId;
    }

    /**
     * Gets all work centers in the given warehouse
     *
     * If a warehouse ID is not provided, uses the repository object's default
     * warehouse ID.
     * 
     * @param string $warehouseId       OPTIONAL The warehouse ID
     * @return WrsGroup_Model_RecordSet A record set of work center objects
     */
    public function getAll($warehouseId = null)
    {
        if (!$warehouseId) {
            $warehouseId = $this->_defaultWarehouseId;
        }

        $select = $this->_db->select()
            ->from(
                'wowcm', 
                array(
                    'warehouse_id'     => 'cwwhid',
                    'work_center_id'   => 'cwwcid',
                    'work_center_name' => 'cwwcds',
                )
            )
            ->where('cwwhid = ?', $warehouseId)
            ->where('cwwcid != ?', '00')
            ->where('TRIM(cwwcds) != ?', 'OPEN');
        $stmt = $select->query();
        $result = $stmt->fetchAll();

        return new WrsGroup_Model_RecordSet(
            $result,
            'WrsGroup_Model_WorkCenter'
        );
    }
}
