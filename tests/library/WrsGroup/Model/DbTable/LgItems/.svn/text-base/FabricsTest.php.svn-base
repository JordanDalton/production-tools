<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class Model_DbTable_LgItems_FabricsTest extends ModelTestCase
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
        $this->_table = $this->_getContainer()->getComponent('FabricsTable');
        $this->_db = $this->_table->getAdapter();
        $this->_config = $this->_getContainer()->getConfig();
        $this->_aPlusSchema = $this->_config->ibmI->aPlusSchema;
    }

    public function testGetFabricOrders()
    {
        $rowset = $this->_table->getFabricOpenOrders();
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset);

        // More than one fabric should be being pulled
        $fabrics = array();
        foreach ($rowset as $row) {
            $fabrics[] = $row->fabric_itno;
        }
        $fabrics = array_unique($fabrics);
        $this->assertGreaterThan(1, count($fabrics));

        // There should be at least one fabric that's on order
        // and at least one mat (make sure we are pulling fabrics AND mats)
        $hasFabric = false;
        $hasMat = false;
        foreach ($rowset as $row) {
            if ($row->fabric_itno == $row->order_itno) {
                $hasFabric = true;
            } else {
                $hasMat = true;
            }
            if ($hasFabric && $hasMat) {
                break;
            }
        }
        $this->assertTrue($hasFabric);
        $this->assertTrue($hasMat);
    }

    public function testGetFabricOrdersForOneFabric()
    {
        $fabricItno = 'LG90009';
        $rowset = $this->_table->getFabricOpenOrders(null, $fabricItno);

        // There shouldn't be any fabrics other than the one given
        $fabrics = array();
        foreach ($rowset as $row) {
            $fabrics[] = $row->fabric_itno;
        }
        $fabrics = array_unique($fabrics);
        $this->assertEquals(1, count($fabrics));
    }

	public function testGetFabrics()
	{
        $rowset = $this->_table->getFabrics();
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset);
        $data = $rowset->current()->toArray();
        $this->assertArrayHasKey('fabric_itno', $data);
        $this->assertTrue(mb_strlen($data['style']) > 1);
        $this->assertTrue(mb_strlen($data['color']) > 1);
	}

    public function testGetFabricsAndChildrenWithOnHandAndPoData()
    {
        $rowset = $this->_table->getFabricsAndChildrenWithOnHandAndPoData();
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset);

        foreach ($rowset as $row) {
            if (isset($fabric) && isset($cut) && isset($mat)) {
                break;
            }
            if ($row->itno == 'LG90002') {
                $fabric = $row;
            }
            if ($row->itno == 'SA-2-01-5001') {
                $cut = $row;
            }
            if ($row->itno == 'SA-1-02-1824-1') {
                $mat = $row;
            }
        }
        $sql = 'SELECT SUM(ibohq1) AS ibohq1 FROM ' . $this->_aPlusSchema .
            '.itbal WHERE ibitno = ? OR ibitno = ?';
        $fabricBaseRow = $this->_db->fetchRow($sql, array('LG90002', 'ZLG90002'));
        $this->assertEquals($fabricBaseRow['ibohq1'],
            round($fabric->qty_on_hand_in_yards, 3));

        $sql = 'SELECT ibohq1 FROM phpaptest.itbal WHERE ibitno = ?';
        $cutBaseRow = $this->_db->fetchRow($sql, 'SA-2-01-5001');
        $sql = 'SELECT bcqtpr FROM phpaptest.bomco WHERE bccmit = ? ' .
            'AND bcprit = ?';
        $cutRatio = $this->_db->fetchRow($sql, array('LG90002', 'SA-2-01-5001'));
        $this->assertEquals(
            (string) ($cutBaseRow['ibohq1'] * $cutRatio['bcqtpr']),
            (string) round($cut->qty_on_hand_in_yards, 3)
        );

        $sql = 'SELECT ibohq1 FROM phpaptest.itbal WHERE ibitno = ?';
        $matBaseRow = $this->_db->fetchRow($sql, 'SA-1-02-1824-1');
        $sql = 'SELECT bcqtpr FROM phpaptest.bomco WHERE bccmit = ? ' .
            'AND bcprit = ?';
        $matRatio = $this->_db->fetchRow(
            $sql,
            array('SA-2-01-5001', 'SA-1-02-1824-1')
        );
        $this->assertEquals(
            (string) round($matBaseRow['ibohq1'] * $cutRatio['bcqtpr'] *
                $matRatio['bcqtpr'], 3),
            (string) round($mat->qty_on_hand_in_yards, 3)
        );
    }

    public function testGetFabricsAndChildrenWithOnHandAndPoDataForOneFabric()
    {
        $rowset = $this->_table->getFabricsAndChildrenWithOnHandAndPoData(
            'LG90002');
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset);
        $this->assertEquals(7, count($rowset));
    }

    public function testGetFabricsAndChildrenInLocations()
    {
        $locations = $this->_config->ignoredLocations->toArray();
        $rowset = $this->_table->getFabricsAndChildrenInLocations($locations);
        foreach ($rowset as $row) {
            if ($row->itno == 'SA-1-02-2036-6') {
                $mat = $row;
                break;
            }
        }
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset);

        $sql = 'SELECT SUM(wbohq1) AS wbohq1 FROM ' . $this->_aPlusSchema .
            '.wmbal WHERE wbitno = ? AND wbloca IN (';
        $sql .= "'" . implode("', '", $locations) . "')";
        $matBaseRow = $this->_db->fetchRow($sql, 'SA-1-02-2036-6');
        $sql = 'SELECT bcqtpr FROM phpaptest.bomco WHERE bccmit = ? ' .
            'AND bcprit = ?';
        $cutRatio = $this->_db->fetchRow($sql, array('LG90011', 'SA-2-01-5066'));
        $matRatio = $this->_db->fetchRow(
            $sql,
            array('SA-2-01-5066', 'SA-1-02-2036-6')
        );
        $this->assertEquals(
            (string) round($matBaseRow['wbohq1'] * $cutRatio['bcqtpr'] *
                $matRatio['bcqtpr'], 3),
            (string) round($mat->qty_on_hand_in_yards, 3)
        );
    }

    public function testGetFabricsAndChildrenInLocationsForOneFabric()
    {
        $locations = $this->_config->ignoredLocations->toArray();
        $rowset = $this->_table->getFabricsAndChildrenInLocations(
            $locations,
            'LG90002'
        );
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset);
        $this->assertEquals(2, count($rowset));
    }

    public function testGetShippedFabricsAndMats()
    {
        $startDate = new WrsGroup_Date('2010-03-01', 'yyyy-MM-dd');
        $endDate = new WrsGroup_Date('2010-04-01', 'yyyy-MM-dd');
        $rowset = $this->_table->getShippedFabricsAndMats($startDate, $endDate);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset);
        foreach ($rowset as $row) {
            if (isset($fabric) && isset($mat)) {
                break;
            }
            if ($row->itno == 'LG90002') {
                $fabric = $row;
            }
            if ($row->itno == 'SA-1-02-1824-1') {
                $mat = $row;
            }
        }
        $sql = 'SELECT SUM(iatrqt) AS iatrqt FROM ' . $this->_aPlusSchema .
            '.iahst WHERE iaitno = ?' .
            ' AND iatrcc = ' . mb_substr($startDate->toString('yyyy'), 0, 2) .
            ' AND iatrdt >= ' . $startDate->toString('yyMMdd') .
            ' AND iatrdt <= ' . $endDate->toString('yyMMdd') .
            ' AND iatrcd = ?';
        $baseFabricRow = $this->_db->fetchRow($sql, array('LG90002', 'Z'));
        $baseMatRow = $this->_db->fetchRow($sql, array('1-02-1824-1', 'Z'));
        $sql = 'SELECT bcqtpr FROM phpaptest.bomco WHERE bccmit = ? ' .
            'AND bcprit = ?';
        $cutRatio = $this->_db->fetchRow($sql, array('LG90002', 'SA-2-01-5001'));
        $matRatio = $this->_db->fetchRow(
            $sql,
            array('SA-2-01-5001', 'SA-1-02-1824-1')
        );
        $this->assertEquals(
            (string) $baseFabricRow['iatrqt'],
            (string) $fabric->shipped_this_month_in_yards
        );
        $this->assertEquals(
            (string) $baseFabricRow['iatrqt'],
            (string) $fabric->shipped_this_month
        );
        $this->assertEquals(
            round($baseMatRow['iatrqt']),
            round($mat->shipped_this_month)
        );
        $this->assertEquals(
            round($baseMatRow['iatrqt'] * $cutRatio['bcqtpr'] * $matRatio['bcqtpr']),
            round($mat->shipped_this_month_in_yards)
        );
    }

    public function testGetShippedFabricsAndMatsWithOneFabric()
    {
        $startDate = new WrsGroup_Date('2010-03-01', 'yyyy-MM-dd');
        $rowset = $this->_table->getShippedFabricsAndMats($startDate, null, 'LG90002');
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset);
        $this->assertEquals(4, count($rowset));
    }

    public function testGetLgFabricShipmentTotals()
    {
        $rowset = $this->_table->getLgFabricShipmentTotals();
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset);
        foreach ($rowset as $row) {
            if ($row->fabric_itno == 'LG90002') {
                break;
            }
        }
        $this->assertEquals(
            (string) 1500,
            (string) round($row->qty_shipped)
        );
    }

    public function testGetLgFabricShipmentTotalsForOneFabric()
    {
        $rowset = $this->_table->getLgFabricShipmentTotals('LG90002');
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset);
        $this->assertEquals(1, count($rowset));
        $row = $rowset->current();
        $this->assertEquals(
            (string) 1500,
            (string) round($row->qty_shipped)
        );
    }
}
