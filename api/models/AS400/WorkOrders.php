<?php
/**
 *  WorkOrders
 *
 *  Description goes here..
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Jan 30, 2012, 10:23:11 AM
 */
class Application_Model_AS400_WorkOrders
{
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;
    
    /*
     * Constructor
     */
    public function __construct($db)
    {
        $this->_db = $db;
    }
    
    /*
     * Test Query
     */
    public function testQuery()
    {
        // DATABASE SELECT STATEMENT
        $select = $this->_db
                       ->select('*')
                       ->from('ITMST')
                       ->where('IMITNO = ?', '23033');
        
        // EXECUTE THE QUERY
        $execute = $select->query();
        
        // RETURN THE RESULTS
        return $execute->fetchAll();
    }
}
/* End of file WorkOrders.php */
/* Location: api/controllers/models/as400/workorders.php */