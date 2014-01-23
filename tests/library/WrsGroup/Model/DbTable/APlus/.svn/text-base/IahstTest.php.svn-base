<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class WrsGroup_Model_DbTable_APlus_IahstTest extends ModelTestCase
{
    /**
     * @var WrsGroup_Model_DbTable_APlus_Iahst
     */
    protected $_table;

    public function setUp()
    {
        parent::setUp();
        $container = $this->_getContainer();
        $this->_table = $container->getComponent('iahstTable');
    }

    public function testGetFabricsWithQtyShipped()
    {
        $date = new WrsGroup_Date();
        $date->setDay(1);
        $rowset = $this->_table->getFabricsWithQtyShipped($date);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset);
    }
}