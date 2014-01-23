<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class WrsGroup_Model_DbTable_APlus_ItbalTest extends ModelTestCase
{
    /**
     * @var WrsGroup_Model_DbTable_APlus_Itbal
     */
    protected $_table;

    public function setUp()
    {
        parent::setUp();
        $container = $this->_getContainer();
        $this->_table = $container->getComponent('itbalTable');
    }

    public function testGetBasicInfoForOneItem()
    {
        $rowset = $this->_table->getBasicInfo('SA-1-02-2036-1');
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset);
        $data = $rowset->current()->toArray();
        $this->assertArrayHasKey('itno', $data);
        $this->assertArrayHasKey('qty_on_hand', $data);
        $this->assertArrayHasKey('qty_on_po', $data);
        $this->assertEquals(1, count($rowset));
    }

    public function testGetBasicInfoForManyItems()
    {
        $rowset = $this->_table->getBasicInfo(array(
            'SA-1-02-2036-1',
            'SA-1-02-2072-1'
        ));
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset);
        $this->assertEquals(2, count($rowset));
    }

    public function testGetLockStatus()
    {
        $locked = $this->_table->getLockStatus('79210');
        $this->assertFalse($locked);
        $locked = $this->_table->getLockStatus('26513');
        $this->assertTrue($locked);
    }

    public function testUnlockItem()
    {
        $this->_table->unlockItem('79210');
        $locked = $this->_table->getLockStatus('79210');
        $this->assertFalse($locked);
    }
}