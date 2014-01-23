<?php
/**
 *  Interface with the WORDT table inside AS/400.
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Feb 8, 2012, 4:11:23 PM
 */
class Rest_Model_Tables_WORDT
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
        $this->_name = (APPLICATION_ENV === 'development') ? null : 'wordt';
        
        // Primary key differs depending upon the application enviornment
        $this->_primary = (APPLICATION_ENV === 'development') ? null : array('czcono', 'czorno', 'czorgn', 'czrtsq');
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Retreive all the work center numbers.
     */
    public function getAllWorkCenters()
    {
        //SELECT DISTINCT CZWRKC FROM APLUS08FIN.wordt
        $select =  $this->_db->select()
                        //->setIntegrityCheck(false)
                        ->distinct(true)
                        ->from('wordt', array(
                            'czwrkc'
                        ))
                        ->order('czwrkc');
        
        // Execute the query and return the results to the controller.
        return $select->query()->fetchAll();
    }
    
    //--------------------------------------------------------------------------
}
/* End of file WORDT.php */
/* Location: api/modules/rest/models/Tables/WORDT.php */