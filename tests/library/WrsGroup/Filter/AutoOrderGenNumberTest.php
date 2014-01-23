<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class WrsGroup_Filter_AutoOrderGenNumberTest extends ModelTestCase
{
    public function testFilterValueWith5Characters()
    {
        $filter = new WrsGroup_Filter_AutoOrderGenNumber();
        $value = $filter->filter('55555');
        $this->assertEquals('5555500', $value);
    }

    public function testFilterValueNotWith5Characters()
    {
        $filter = new WrsGroup_Filter_AutoOrderGenNumber();
        $value = $filter->filter('5555501');
        $this->assertEquals('5555501', $value);
    }
}
