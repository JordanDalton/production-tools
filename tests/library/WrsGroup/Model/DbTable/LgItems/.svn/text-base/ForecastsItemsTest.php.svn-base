<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class Model_DbTable_LgItems_ForecastsItemsTest extends ModelTestCase
{
    /**
     * @var WrsGroup_Model_DbTable_Fabrics
     */
    protected $_table;

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * @var Zend_Config
     */
    protected $_config;

    /**
     * @var string
     */
    protected $_aPlusSchema;

    public function setUp()
    {
        parent::setUp();
        $this->_table = $this->_getContainer()->
            getComponent('ForecastsItemsTable');
        $this->_db = $this->_table->getAdapter();
        $this->_config = $this->_getContainer()->getConfig();
        $this->_aPlusSchema = $this->_config->ibmI->aPlusSchema;
    }

    public function testGetMonthRange()
    {
        $row = $this->_table->getMonthRange(1);
        $attr = Model_Forecast::FIRST_MONTH;
        $this->assertEquals('2010-02-01', $row->$attr);
        $attr = Model_Forecast::LAST_MONTH;
        $this->assertEquals('2010-08-01', $row->$attr);
    }

    public function testGetForecastDataForAllFabrics()
    {
        $rowset = $this->_table->getForecastData(1);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset);
        $row = $rowset->current();
        $this->assertEquals('2010-02-01', $row->month);
        $rowset->next();
        $row = $rowset->current();
        $this->assertEquals('2010-03-01', $row->month);
    }

    public function testGetForecastForFabricOutlook()
    {
        $rowset = $this->_table->getForecastForFabricOutlook(1, null, 0.388);
        foreach ($rowset as $row) {
            if ($row->forecast_itno == 'LG90002'
                    && $row->month == '2010-02-01') {
                $rowWithConversion = $row;
                break;
            }
        }
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset);
        $rowset = $this->_table->getForecastForFabricOutlook(1);
        foreach ($rowset as $row) {
            if ($row->forecast_itno == 'LG90002'
                    && $row->month == '2010-02-01') {
                $rowWithoutConversion = $row;
                break;
            }
        }
        $this->assertLessThan(
            $rowWithoutConversion->yards,
            $rowWithConversion->yards
        );
    }

    public function testGetForecastDataForFabricOutlookForOneFabric()
    {
        $rowset = $this->_table->getForecastForFabricOutlook(1, 'LG90002', 0.388);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset);
        // Seven different months for two mats and one fabric
        $this->assertEquals(21, count($rowset));
        $row1 = $rowset->current();
        $rowset->next();
        $row2 = $rowset->current();
        $this->assertEquals($row1->forecast_itno, $row2->forecast_itno);
        $this->assertGreaterThan($row1->month, $row2->month);
    }
}
