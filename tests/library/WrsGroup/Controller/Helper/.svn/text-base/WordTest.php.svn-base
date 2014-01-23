<?php
require_once realpath(APPLICATION_PATH . '/../tests/ControllerTestCase.php');

class WordTestController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->word('index', 'somefile.doc');
    }

    public function indexAction()
    {
        // Set view script path to an empty file to avoid an error
        // when the view script is not found
        $this->view->setScriptPath(dirname(__FILE__) . '/fixture');
    }
}

class WrsGroup_Controller_Helper_WordTest extends ControllerTestCase
{
    public function testWordHelper()
    {
        $this->dispatch('/word-test/index/format/word');
        $this->assertHeaderContains('Content-Type', 'application/vnd.ms-word');
        $this->assertHeaderContains('Content-Disposition', 
                                    'attachment;filename="somefile.doc"');
        $this->assertController('word-test');
        $this->assertAction('index');
        $this->assertResponseCode(200);
    }
}
