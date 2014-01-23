<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class BomcoTest extends ModelTestCase
{
    /**
     * @var WrsGroup_Model_DbTable_Bomco
     */
    protected $_table;

    public function setUp()
    {
        parent::setUp();
        $container = $this->_getContainer();
        $this->_table = $container->getComponent('bomcoTable');
    }

    public function testGetParentItemsWithOneChild()
    {
        $rowset = $this->_table->getParentItems(
            'SA-1-02-2036-1'
        );
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset);
        $this->assertGreaterThan(1, count($rowset));
    }

    public function testGetParentItemsWithManyChildren()
    {
        $rowset = $this->_table->getParentItems(
            'SA-1-02-2036-1',
            'SA-1-02-2036-2'
        );
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset);
        $this->assertGreaterThan(1, count($rowset));
    }
}