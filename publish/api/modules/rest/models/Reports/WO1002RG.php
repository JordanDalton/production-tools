<?php
/**
 *  This model is used to simulate the WO1010RG report in AS/400
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Feb 7, 2012, 8:14:44 AM
 */
class Rest_Model_Reports_WO1002RG
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
        if(APPLICATION_ENV === 'development')
        {
            $errorArray = array(
                'status' => 'failed',
                'version'=> '1.0',
                'response' => array(
					'message' => 'Rest_Model_Reports_WO1002RG is only supported during production mode.'
            ));
            
            echo json_encode($errorArray);
            exit;
        }
        
        // Retrieve the db adapter and assign it.
        $this->_db = $db;

        // Get the instance of the front controller
        $this->_front       = Zend_Controller_Front::getInstance();
        
        // Get the container from the front controller
        $this->_container   = $this->_front->getParam('bootstrap')->getContainer();
        
        //Get the Zend_Config from the front container
        $this->_config      = $this->_container->getConfig();
               
        // Table names differ depending upon the enviornment
        $this->_name = (APPLICATION_ENV === 'development') ? false : 'wohed';
        
        // Primary key differs depending upon the application enviornment
        $this->_primary = (APPLICATION_ENV === 'development') ? null : array('escono', 'esorno', 'esorgn');
    }
    
    //--------------------------------------------------------------------------

    /**
     * Work Order/Back Order Report
     * 
     * @param array The array of custom parameters passed.
     * @return mixed
     */
    public function getReport($params)
    {        
        $select =  $this->_db->select()
                        //->setIntegrityCheck(false)
                        ->from($this->_name, array(
							'item_number'	=>	'esprit',
                            'order_number'  =>  'esorno',
                            'order_quantity'=>  'esqtor',
							'status'		=> 	'esorst'
                        ))
                        ->joinLeft(
                            'itbal', 
                            'itbal.ibitno = wohed.esprit', 
                            array(
                                'back_order_quantity' => 'ibboq1',
                                //'item_number'        => 'ibitno',
                        ))
                        ->joinLeft(
                            'itmst', 
                            'itmst.imitno = wohed.esprit', 
                            array(
                                'item_description'  => 'CONCAT(imitd1, imitd2)',
								//'cost' 				=> 'imlpr1',
								//'back_order_value' 	=> '(imlpr1 * itbal.ibboq1)',
                        ))
                        ->joinLeft(
                            'ordet', 
                            'ordet.oditno = wohed.esprit', 
                            array(
                                'back_order_value'  => 'SUM(odlnvl)',
                        ));
        
		//$select->limit(10);
		
        /**********************************************************************/
		
        // If order number param is set, the attempt to use it.
        if(isSet($params['order-number']))
            $select->where('wohed.esorno = ?', $params['order-number']);
        
        /**********************************************************************/
        
        // If order status param is set, the attempt to use it.
        if(isSet($params['order-status']))
            $select->where('wohed.esorst IN (?)', explode(',', $params['order-status']));
        
        /**********************************************************************/
        
        // If item number param is set, the attempt to use it.
        if(isSet($params['item-number']))
            $select->where('wohed.esprit = ?', $params['item-number']);
        
        /**********************************************************************/
		
		$select->group(array(
			'wohed.esorno', 
			'wohed.esprit', 
			'wohed.esqtor', 
			'wohed.esorst', 
			'itbal.ibboq1', 
			'CONCAT(imitd1, imitd2)'
		));
		
		/* DEBUG OUT *\/
		return $select->__toString();
		/**/
        
        // Execute the query
        return $select->query()->fetchAll();
    }
    
    //--------------------------------------------------------------------------
}
/* End of file WO1002RG.php */
/* Location: api/modules/rest/models/Reports/WO1002RG.php */