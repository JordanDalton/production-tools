<?php
require_once realpath(APPLICATION_PATH . '/../tests/ControllerTestCase.php');

class CsvTestController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->csv('index', 'somefile.csv');
    }

    public function indexAction()
    {
        // Set view script path to an empty file to avoid an error
        // when the view script is not found
        $this->view->setScriptPath(dirname(__FILE__) . '/fixture');
    }
}

class WrsGroup_Controller_Helper_CsvTest extends ControllerTestCase
{
    public function testCsvHelper()
    {
        $this->dispatch('/csv-test/index/format/csv');
        $this->assertHeaderContains('Content-Type', 'text/csv');
        $this->assertHeaderContains('Content-Disposition', 
                                    'attachment;filename="somefile.csv"');
        $this->assertController('csv-test');
        $this->assertAction('index');
        $this->assertResponseCode(200);
    }
}
