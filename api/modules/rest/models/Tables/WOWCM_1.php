<?php
/**
 *  Interface with the WOWCM (work centers) table inside AS/400.
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Feb 13, 2012, 2:41:06 PM
 */
class Rest_Model_Tables_WOWCM extends Zend_Db_Table_Abstract
{
    /**
     * @var string DB Table Name
     */
    protected $_name;
	/**
	 * @var mixed
	 */
	protected $_primary;
    /**
     * @var string The default warehouse ID
     */
    protected $_defaultWarehouseId;
    
    //--------------------------------------------------------------------------
    
    /**
     * Constructor
     */
    public function init()
    {
        // Get the instance of the front controller
        $this->_front       = Zend_Controller_Front::getInstance();
        
        // Get the container from the front controller
        $this->_container   = $this->_front->getParam('bootstrap')->getContainer();
        
        //Get the Zend_Config from the front container
        $this->_config      = $this->_container->getConfig();
               
        // Table names differ depending upon the enviornment
        $this->_name = (APPLICATION_ENV === 'development') ? null : 'wowcm';
        
        // Primary key differs depending upon the application enviornment
        $this->_primary = (APPLICATION_ENV === 'development') ? null : array('cwwhid', 'cwwcid');
        
        // Set the default default warehouse id
        $this->setDefaultWarehouseId($this->_config->defaultWarehouseId);
    }

    //--------------------------------------------------------------------------
    
    /**
     * Sets the default warehouse ID
     * 
     * @param string $warehouseId The warehouse ID
     */
    public function setDefaultWarehouseId($warehouseId)
    {
        $this->_defaultWarehouseId = $warehouseId;
    }
    
    //--------------------------------------------------------------------------
    
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
        // If warehouse id is not set, then us our default one
        if(!$warehouseId) $warehouseId = $this->_defaultWarehouseId;
	 
        // Prepare our query
        $select = $this->select()
                       ->setIntegrityCheck(false)
                       ->from($this->_name, array(
                           'warehouse_id'     => 'TRIM(cwwhid)',
                           'work_center_id'   => 'TRIM(cwwcid)',
                           'work_center_name' => 'TRIM(cwwcds)',
                       ))
                       ->where('cwwhid = ?', $warehouseId)
                       ->where('cwwcid != ?', '00')
                       ->where('TRIM(cwwcds) != ?', 'OPEN');
        
        // Execute the query retrieve the results;
        $results = $select->query()->fetchAll();
        
        // Return the results to the controller.
        return $results;
    }
    
    //--------------------------------------------------------------------------
}
/* End of file WOWCM.php */
/* Location: api/modules/rest/models/Tables/WOWCM.php */