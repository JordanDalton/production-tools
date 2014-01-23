<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class WrsGroup_View_Helper_DateFormatTest extends ModelTestCase
{
    public function testDateFormatWithNonMatchingString()
    {
        $helper = new WrsGroup_View_Helper_DateFormat();
        $helper->setView(new Zend_View());
        $string = $helper->dateFormat('abcdefg');
        $this->assertEquals('abcdefg', $string);
    }

    public function testDateFormatWithGoodString()
    {
        $helper = new WrsGroup_View_Helper_DateFormat();
        $helper->setView(new Zend_View());
        $string = $helper->dateFormat('2010-04-05');
        $this->assertEquals('04/05/2010', $string);
    }
}
