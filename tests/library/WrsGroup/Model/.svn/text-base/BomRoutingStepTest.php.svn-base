<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class WrsGroup_Model_BomRoutingStepTest extends ModelTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->_bomRoutingStep = new WrsGroup_Model_BomRoutingStep(array(
            'cgprit' => 'SA-1-02-2036-1',
            'cgcono' => 0,
            'cgopds' => 'ASSEMBLE',
            'cgsrlh' => 1.57
        ));
    }

    public function testGetParentItemNumber()
    {
        $this->assertEquals(
            'SA-1-02-2036-1',
            $this->_bomRoutingStep->getParentItemNumber()
        );
    }

    public function testGetLaborHours()
    {
        $this->assertEquals(1.57, $this->_bomRoutingStep->getLaborHours());
    }

    public function testGetOperationDescription()
    {
        $this->assertEquals(
            'ASSEMBLE', 
            $this->_bomRoutingStep->getOperationDescription()
        );
    }
}
