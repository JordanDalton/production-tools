<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class WrsGroup_Decorator_MatSuffixTest extends ModelTestCase
{
    public function testGetDescriptionWithSuffix()
    {
        $item = new WrsGroup_Model_Item(array('imitno' => '1-02-2036-1-R'));
        $decorator = new WrsGroup_Decorator_MatSuffix($item);
        $decorator->setSuffixConfig(new Zend_Config(array(
            'R' => 'Retail'
        )));
        $this->assertEquals('Retail', $decorator->getDescription());
    }

    public function testGetDescriptionForDropShip()
    {
        $item = new WrsGroup_Model_Item(array('imitno' => '1-02-2036-1'));
        $decorator = new WrsGroup_Decorator_MatSuffix($item);
        $decorator->setSuffixConfig(new Zend_Config(array(
            'R' => 'Retail'
        )));
        $this->assertEquals('Drop Ship', $decorator->getDescription());
    }

    public function testGetDescriptionWithNoMatch()
    {
        $item = new WrsGroup_Model_Item(array('imitno' => '1-02-2036-1-ZZZ'));
        $decorator = new WrsGroup_Decorator_MatSuffix($item);
        $decorator->setSuffixConfig(new Zend_Config(array(
            'R' => 'Retail'
        )));
        $this->assertEquals('ZZZ', $decorator->getDescription());
    }

    public function testGetDescriptionWithNoHyphensInItemNumber()
    {
        $item = new WrsGroup_Model_Item(array('imitno' => '55555'));
        $decorator = new WrsGroup_Decorator_MatSuffix($item);
        $decorator->setSuffixConfig(new Zend_Config(array(
            'R' => 'Retail'
        )));
        $this->assertEquals('', $decorator->getDescription());
    }
}
