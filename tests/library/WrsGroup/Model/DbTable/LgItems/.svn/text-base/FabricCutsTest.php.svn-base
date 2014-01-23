<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class Model_DbTable_LgItems_FabricCutsTest extends ModelTestCase
{
	public function testSelectFabricCutsForMatUnitSize()
	{
        $table = $this->_getContainer()->getComponent('FabricCutsTable');
        $select = $table->selectFabricCutsForMatUnitSize(1);
        $this->assertType('WrsGroup_Db_Table_OdbcDb2_Select', $select);
	}

    public function testGetFabricCutsWithMatUnits()
    {
        $table = $this->_getContainer()->getComponent('FabricCutsTable');
        $rowset = $table->getFabricCutsWithMatUnits();
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset);
        $data = $rowset->current()->toArray();
        $this->assertArrayHasKey('fabcut_itno', $data);
        $this->assertArrayHasKey('fabric_itno', $data);
        $this->assertTrue($data['mat_units_per_fabric_cut'] > 0);
    }
}
