<?php
/**
 *  Interface with the WOHED table inside AS/400.
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Feb 8, 2012, 4:11:23 PM
 */
class Rest_Model_Tables_WOHED
{
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;
    /**
     * @var string DB Table Name
     */
    protected $_name;
	/**
	 * @var mixed
	 */
	protected $_primary;
    
    //--------------------------------------------------------------------------
    
    /**
     * Constructor
     */
    public function __construct($db)
    {
        // Retrieve the db adapter and assign it.
        $this->_db = $db;

        // Get the instance of the front controller
        $this->_front       = Zend_Controller_Front::getInstance();
        
        // Get the container from the front controller
        $this->_container   = $this->_front->getParam('bootstrap')->getContainer();
        
        //Get the Zend_Config from the front container
        $this->_config      = $this->_container->getConfig();
               
        // Table names differ depending upon the enviornment
        $this->_name = (APPLICATION_ENV === 'development') ? null : 'wohed';
        
        // Primary key differs depending upon the application enviornment
        $this->_primary = (APPLICATION_ENV === 'development') ? null : array('escono', 'esorno', 'esorgn');
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Retreive due date for a particular work order or item.
     * 
     * @param string $key   The field that we will be query. Only acceps 'workOrderNumber' or 'itemNumber'.
     * @param type $value   The value for the field we're searching by.
     * @return array        The results array.
     */    
    public function getDueDate($key, $value)
    {
        // Set the $key
        $key = 'workOrder' ? 'wohed.esorno' : 'wohed.esprit';
        
        // Prepare our query
        $select = $this->_db
                       ->select()
                       ->from($this->_name, array(
                           'due_date' => 'CONCAT(wohed.esducc, wohed.esdudt)'
                       ))
                       ->where("{$key} = ?", $value);

        // Execute query and return the results.
        return $select->query()->fetchAll();               
    }
    
    //--------------------------------------------------------------------------
}
/* End of file WOHED.php */
/* Location: api/modules/rest/models/Tables/WOHED.php */