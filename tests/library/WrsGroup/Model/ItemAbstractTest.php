<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class WrsGroup_Model_ItemAbstractTest extends ModelTestCase
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

    public function testGetDescription()
    {
        $this->assertEquals(
            'Description 1Description 2',
            $this->_item->getDescription()
        );
    }
}
