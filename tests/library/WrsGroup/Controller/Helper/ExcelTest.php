<?php
require_once realpath(APPLICATION_PATH . '/../tests/ControllerTestCase.php');

class ExcelTestController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->excel('index', 'somefile.xls');
    }

    public function indexAction()
    {
        // Set view script path to an empty file to avoid an error
        // when the view script is not found
        $this->view->setScriptPath(dirname(__FILE__) . '/fixture');
    }
}

class WrsGroup_Controller_Helper_ExcelTest extends ControllerTestCase
{
    public function testExcelHelper()
    {
        $this->dispatch('/excel-test/index/format/excel');
        $this->assertHeaderContains('Content-Type', 'application/vnd.ms-excel');
        $this->assertHeaderContains('Content-Disposition', 
            'attachment;filename="somefile.xls"');
        $this->assertController('excel-test');
        $this->assertAction('index');
        $this->assertResponseCode(200);
    }
}
