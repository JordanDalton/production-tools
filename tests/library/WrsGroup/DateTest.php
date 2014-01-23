<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class DateTest extends ModelTestCase
{
    public function testIsBusinessDay()
    {
        $date = new WrsGroup_Date('2010-02-25', 'yyyy-MM-dd');
        $this->assertTrue($date->isBusinessDay());
    }
}