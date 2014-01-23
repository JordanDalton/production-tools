<?php
/**
 *  Interface with the PODET table inside AS/400.
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Feb 6, 2012, 3:21:11 PM
 */
class Rest_Model_Tables_PODET
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
        $this->_name = (APPLICATION_ENV === 'development') ? new Exception('Not Supported') : 'podet';
        
        // Primary key differs depending upon the application enviornment
        $this->_primary = (APPLICATION_ENV === 'development') ? null : array('pdcono', 'pdorid', 'pdlnsq');
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Get the U/M for a given item number.
     * @param string $item_number The item number we're requesting data for.
     * @return object
     */
    public function getUmByItemNumber($item_number)
    {
        $select =  $this->_db->select()
                        //->setIntegrityCheck(false)
                        ->from($this->_name, array(
                            'pdunms'
                        ))
                        ->where('pditno = ?', $item_number);
        
        // Execute the query
        $results = $select->query()->fetchObject();
        
        // retun the results to the controller.
        return $results;
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Retreive due date for a particular work order or item.
     * 
     * @param type $value   The value for the field we're searching by.
     * @return array        The results array.
     */    
    public function getDueDate($value)
    {
        // Set the $key
        $key = 'workOrder' ? 'wohed.esorno' : 'wohed.esprit';
        
        // Prepare our query
        $select = $this->_db
                       ->select()
                       ->from($this->_name, array(
                           'due_date' => 'CONCAT(podet.pdducc, podet.pddudt)'
                       ))
                       ->where("podet.pditno = ?", $value);

        // Execute query and return the results.
        return $select->query()->fetchAll();               
    }
    
    //--------------------------------------------------------------------------
}
/* End of file PODET.php */
/* Location: api/modules/rest/models/Tables/PODET.php */