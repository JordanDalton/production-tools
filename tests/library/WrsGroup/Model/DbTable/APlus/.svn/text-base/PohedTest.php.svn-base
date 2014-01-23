<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class WrsGroup_Model_DbTable_APlus_PohedTest extends ModelTestCase
{
    /**
     * @var WrsGroup_Model_DbTable_APlus_Itmst
     */
    protected $_table;

    public function setUp()
    {
        parent::setUp();
        $container = $this->_getContainer();
        $this->_table = $container->getComponent('pohedTable');
    }

    public function testGetLockStatus()
    {
        $locked = $this->_table->getLockStatus('N00030');
        $this->assertFalse($locked);
        $locked = $this->_table->getLockStatus('243819');
        $this->assertTrue($locked);
    }

    public function testUnlockPo()
    {
        $this->_table->unlockPo('N00030');
        $locked = $this->_table->getLockStatus('N00030');
        $this->assertFalse($locked);
    }
}