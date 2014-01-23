<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class WrsGroup_Decorator_ItemCleanTest extends ModelTestCase
{
    public function testGetDescriptionWithPunctuationAtEndOfDesc1()
    {
        $item = new WrsGroup_Model_Item(array(
            'imitno' => '26500C',
            'imitd1' => 'Custom BSE Model Easel Display,',
            'imitd2' => 'Beige                          ',
        ));
        $decorator = new WrsGroup_Decorator_ItemClean($item);
        $description = $decorator->getDescription();
        $this->assertEquals('Custom BSE Model Easel Display, Beige', $description);
    }

    public function testGetDescriptionWithSpaceAtStartOfDesc2()
    {
        $item = new WrsGroup_Model_Item(array(
            'imitno' => 'LG92033',
            'imitd1' => 'Ostrich Atlantic Blue Label For',
            'imitd2' => ' 4 x 4 Swatch Kit              ',
        ));
        $decorator = new WrsGroup_Decorator_ItemClean($item);
        $description = $decorator->getDescription();
        $this->assertEquals('Ostrich Atlantic Blue Label For 4 x 4 Swatch Kit', $description);
    }

    public function testGetDescriptionWithSpaceAtEndofDesc1()
    {
        $item = new WrsGroup_Model_Item(array(
            'imitno' => '26406',
            'imitd1' => 'TSE Model, Beige (two lumps in ',
            'imitd2' => 'one testicle)                  ',
        ));
        $decorator = new WrsGroup_Decorator_ItemClean($item);
        $description = $decorator->getDescription();
        $this->assertEquals('TSE Model, Beige (two lumps in one testicle)', $description);
    }

    public function testGetDescriptionWithWordSplitAcrossDesc1AndDesc2()
    {
        $item = new WrsGroup_Model_Item(array(
            'imitno' => '26407',
            'imitd1' => 'Uncircumcised Condom Teaching M',
            'imitd2' => 'odel Beige                     ',
        ));
        $decorator = new WrsGroup_Decorator_ItemClean($item);
        $description = $decorator->getDescription();
        $this->assertEquals('Uncircumcised Condom Teaching Model Beige', $description);
    }
}
