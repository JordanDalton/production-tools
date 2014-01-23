<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class WrsGroup_Model_DbTable_APlus_WmbalTest extends ModelTestCase
{
    /**
     * @var WrsGroup_Model_DbTable_APlus_Wmbal
     */
    protected $_table;

    public function setUp()
    {
        parent::setUp();
        $container = $this->_getContainer();
        $this->_table = $container->getComponent('wmbalTable');
    }

    public function testGetTotalInLocationForOneItemOneLocation()
    {
        $rowset = $this->_table->getTotalQtyInLocations(
            'SA-1-02-2036-1',
            array('A  0199A')
        );
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset);
        $data = $rowset->current()->toArray();
        $this->assertArrayHasKey('itno', $data);
        $this->assertArrayHasKey('qty', $data);
        $this->assertEquals(1, count($rowset));
    }

    public function testGetTotalInLocationForOneItemManyLocations()
    {
        $rowset = $this->_table->getTotalQtyInLocations(
            'SA-1-02-2036-1',
            array('DMG0101A', 'A  0199A')
        );
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset);
        $this->assertEquals(1, count($rowset));
    }

    public function testGetTotalInLocationForManyItemsManyLocations()
    {
        $rowset = $this->_table->getTotalQtyInLocations(
            array('SA-1-02-2036-1', 'SA-1-02-2036-6'),
            array('DMG0101A', 'A  0199A')
        );
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset);
        $this->assertEquals(2, count($rowset));
    }
}