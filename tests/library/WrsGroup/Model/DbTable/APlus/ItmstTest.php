<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class WrsGroup_Model_DbTable_APlus_ItmstTest extends ModelTestCase
{
    /**
     * @var WrsGroup_Model_DbTable_APlus_Itmst
     */
    protected $_table;

    public function setUp()
    {
        parent::setUp();
        $container = $this->_getContainer();
        $this->_table = $container->getComponent('itmstTable');
    }

    public function testGetLockStatus()
    {
        $locked = $this->_table->getLockStatus('79210');
        $this->assertFalse($locked);
        $locked = $this->_table->getLockStatus('39301');
        $this->assertTrue($locked);
    }

    public function testUnlockItem()
    {
        $this->_table->unlockItem('79210');
        $locked = $this->_table->getLockStatus('79210');
        $this->assertFalse($locked);
    }
}