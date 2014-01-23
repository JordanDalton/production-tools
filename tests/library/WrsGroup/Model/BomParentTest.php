<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class WrsGroup_Model_BomParentTest extends ModelTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->_bomParent = new WrsGroup_Model_BomParent(array(
            'bpprit' => '1-02-2036-1',
            'bpcono' => 0,
            'bplbhr' => 1.57
        ));
    }

    public function testGetParentItemNumber()
    {
        $this->assertEquals(
            '1-02-2036-1',
            $this->_bomParent->getParentItemNumber()
        );
    }

    public function testGetLaborHours()
    {
        $this->assertEquals(1.57, $this->_bomParent->getLaborHours());
    }
}
