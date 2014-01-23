<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class WrsGroup_Decorator_ItemAbstractTest extends ModelTestCase
{
    public function testGetDescription1()
    {
        $item = new WrsGroup_Model_Item(array(
            'imitd1' => 'desc 1',
            'imitd2' => 'desc 2',
        ));
        $decorator = new WrsGroup_Decorator_ItemClean($item);
        $this->assertEquals('desc 1', $decorator->getDescription1());
    }

    public function testGetDescription2()
    {
        $item = new WrsGroup_Model_Item(array(
            'imitd1' => 'desc 1',
            'imitd2' => 'desc 2',
        ));
        $decorator = new WrsGroup_Decorator_ItemClean($item);
        $this->assertEquals('desc 2', $decorator->getDescription2());
    }
}
