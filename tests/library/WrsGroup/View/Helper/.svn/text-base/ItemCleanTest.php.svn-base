<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class WrsGroup_View_Helper_ItemCleanTest extends ModelTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->view = new Zend_View();
        $this->helper = new WrsGroup_View_Helper_ItemClean();
        $this->helper->setView($this->view);
    }

    public function testItemClean()
    {
        $item = new WrsGroup_Model_Item(array(
            'imitno' => '77777',
            'imitd1' => 'Description 1',
            'imitd2' => 'Description 2',
        ));
        $this->assertEquals(
            'Description 1 Description 2',
            $this->helper->itemClean($item)
        );
    }
}
