<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class WrsGroup_Model_BomComponentTest extends ModelTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->_bomComponent = new WrsGroup_Model_BomComponent(array(
            'bcprit' => '1-02-2036-1',
            'bccmit' => 'SA-1-02-2036-1',
            'bccono' => 0,
            'bcqtpr' => 1
        ));
    }

    public function testGetParentItemNumber()
    {
        $this->assertEquals(
            '1-02-2036-1',
            $this->_bomComponent->getParentItemNumber()
        );
    }

    public function testGetComponentItemNumber()
    {
        $this->assertEquals(
            'SA-1-02-2036-1', 
            $this->_bomComponent->getComponentItemNumber()
        );
    }

    public function testGetQuantityPerParent()
    {
        $this->assertEquals(
            1,
            $this->_bomComponent->getQuantityPerParent()
        );
    }
}
