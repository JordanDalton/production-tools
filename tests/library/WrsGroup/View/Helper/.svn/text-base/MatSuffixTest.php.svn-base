<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class WrsGroup_View_Helper_MatSuffixTest extends ModelTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->view = new Zend_View();
        $this->helper = new WrsGroup_View_Helper_MatSuffix();
        $this->helper->setView($this->view);
    }

    public function testMatSuffix()
    {
        $item = new WrsGroup_Model_Item(array(
            'imitno' => '77777-AAAAAA',
            'imitd1' => 'Description 1',
            'imitd2' => 'Description 2',
        ));
        $this->assertEquals(
            'AAAAAA',
            $this->helper->matSuffix($item)
        );
    }
}
