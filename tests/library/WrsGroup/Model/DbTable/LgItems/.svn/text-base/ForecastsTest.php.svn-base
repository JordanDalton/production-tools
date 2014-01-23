<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class Model_DbTable_LgItems_ForecastsTest extends ModelTestCase
{
	public function testGetForecast()
	{
        $table = $this->_getContainer()->getComponent('ForecastsTable');
        $row = $table->getForecast(1);
        $this->assertType('Zend_Db_Table_Row_Abstract', $row);
        $this->assertEquals(1, $row->forecast_id);
	}
}
