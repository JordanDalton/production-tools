<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class Model_DbTable_LgItems_LgShipmentsItemsTest extends ModelTestCase
{
	public function testGetPendingLgShipmentItems()
	{
        $table = $this->_getContainer()->getComponent('LgShipmentsItemsTable');
        $rowset = $table->getPendingLgShipmentItems();
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset);
        $this->assertTrue(count($rowset) > 1);
	}
}
