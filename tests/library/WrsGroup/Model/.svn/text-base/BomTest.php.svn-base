<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class WrsGroup_Model_BomTest extends ModelTestCase
{
    /**
     * @var WrsGroup_Model_Bom
     */
    protected $_bom;

    public function setUp()
    {
        parent::setUp();
        $this->_bom = new WrsGroup_Model_Bom(array(
            'bomLevels' => new WrsGroup_Model_RecordSet(array(
                array(
                    'level' => 1,
                    'bomComponents' => new WrsGroup_Model_RecordSet(array(
                        array(
                            'bcprit' => '1-1111',
                            'bccmit' => 'SA-1-1111'
                        ),
                        array(
                            'bcprit' => '1-1111',
                            'bccmit' => 'LG11111'
                        ),
                    ), 'WrsGroup_Model_BomComponent'),
                ),
                array(
                    'level' => 2,
                    'bomComponents' => new WrsGroup_Model_RecordSet(array(
                        array(
                            'bcprit' => 'SA-1-1111',
                            'bccmit' => 'SA-2-2222'
                        ),
                        array(
                            'bcprit' => 'SA-1-1111',
                            'bccmit' => 'LG22222'
                        ),
                    ), 'WrsGroup_Model_BomComponent'),
                    'bomRoutingSteps' => new WrsGroup_Model_RecordSet(array(
                        array(
                            'cgprit' => 'SA-1-1111',
                            'cgsrlh' => 0.5,
                        ),
                        array(
                            'cgprit' => 'SA-1-1111',
                            'cgsrlh' => 1.0,
                        ),
                    ), 'WrsGroup_Model_BomRoutingStep'),
                ),
            ), 'WrsGroup_Model_BomLevel'),
        ));
    }

    public function testGetBomComponentsAtLevel1()
    {
        $components = $this->_bom->getBomComponentsForLevel(1);
        $this->assertEquals(2, count($components));
        $this->assertInstanceOf('WrsGroup_Model_RecordSet', $components);
        $this->assertEquals('SA-1-1111', $components->current()->bccmit);
    }

    public function testGetBomComponentsAtLevel2()
    {
        $components = $this->_bom->getBomComponentsForLevel(2);
        $this->assertEquals(2, count($components));
        $this->assertInstanceOf('WrsGroup_Model_RecordSet', $components);
        $this->assertEquals('SA-2-2222', $components->current()->bccmit);
    }

    public function testGetBomComponentsAtNonExistentLevel()
    {
        $components = $this->_bom->getBomComponentsForLevel(4);
        $this->assertNull($components);
    }

    public function testGetBomRoutingStepsAtLevel1()
    {
        $routingSteps = $this->_bom->getBomRoutingStepsForLevel(1);
        $this->assertNull($routingSteps);
    }

    public function testGetBomRoutingStepsAtLevel2()
    {
        $routingSteps = $this->_bom->getBomRoutingStepsForLevel(2);
        $this->assertEquals(2, count($routingSteps));
        $this->assertEquals(0.5, $routingSteps->current()->cgsrlh);
    }
}
