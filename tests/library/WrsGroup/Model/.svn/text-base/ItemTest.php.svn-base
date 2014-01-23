<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class WrsGroup_Model_ItemTest extends ModelTestCase
{
    /**
     * @var WrsGroup_Model_Item
     */
    protected $_item;

    public function setUp()
    {
        parent::setUp();
        $this->_item = new WrsGroup_Model_Item(array(
            'imitno' => '55555',
            'imitd1' => 'Description 1',
            'imitd2' => 'Description 2',
        ));
    }

    public function testGetDescription1()
    {
        $this->assertEquals('Description 1', $this->_item->getDescription1());
    }

    public function testGetDescription2()
    {
        $this->assertEquals('Description 2', $this->_item->getDescription2());
    }

    public function testGetItemNumber()
    {
        $this->assertEquals('55555', $this->_item->getItemNumber());
    }
}
