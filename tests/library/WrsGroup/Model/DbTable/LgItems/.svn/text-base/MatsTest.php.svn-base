<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class WrsGroup_Model_DbTable_LgItems_MatsTest extends ModelTestCase
{
    public function testGetMats()
    {
        $table = $this->_getContainer()->getComponent('matsTable');
        $rowset = $table->getMats();
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset);
        $data = $rowset->current()->toArray();
        $this->assertArrayHasKey('mat_units', $data);
    }

    public function testGetMatsWithRestrictionCodes()
    {
        $table = $this->_getContainer()->getComponent('matsTable');
        $rowset = $table->getMats(array('SSP'));
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset);
        $restrictedMatFound = false;
        foreach ($rowset as $row) {
            if (mb_strpos($row->restriction_code, 'SSP') !== false) {
                $restrictedMatFound = true;
                break;
            }
        }
        $this->assertFalse($restrictedMatFound);
    }

    public function testGetMatsShipped()
    {
        $table = $this->_getContainer()->getComponent('matsTable');
        $startDate = new WrsGroup_Date('2010-03-01', 'yyyy-MM-dd');
        $rowset = $table->getMatsShipped($startDate);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset);
        $data = $rowset->current()->toArray();
        $this->assertArrayHasKey('qty_shipped', $data);
    }

    public function testGetMatOpenOrders()
    {
        $table = $this->_getContainer()->getComponent('matsTable');
        $rowset = $table->getMatOpenOrders();
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset);
        $data = $rowset->current()->toArray();
        $this->assertTrue($data['qty_ordered'] > 0);
        $this->assertArrayHasKey('order_number', $data);
        $this->assertArrayHasKey('order_generation_number', $data);
        $this->assertArrayHasKey('mat_itno', $data);
        $this->assertRegExp('/\d{4}-\d{2}-\d{2}/', $data['entry_date']);
        $this->assertRegExp('/\d{4}-\d{2}-\d{2}/', $data['requested_ship_date']);
    }
}
