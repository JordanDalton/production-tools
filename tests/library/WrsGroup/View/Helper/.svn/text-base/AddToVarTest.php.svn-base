<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class WrsGroup_View_Helper_AddToVarTest extends ModelTestCase
{
    public function testAddToVar()
    {
        $helper = new WrsGroup_View_Helper_AddToVar();
        $helper->setView(new Zend_View());
        $helper->addToVar(1, 'col1');
        $this->assertEquals(1, $helper->view->col1);
        $helper->addToVar(2, 'col1');
        $this->assertEquals(3, $helper->view->col1);
        $helper->addToVar(33.20, 'col2');
        $helper->addToVar(6.20, 'col2');
        $this->assertEquals(39.40, $helper->view->col2, '', 0.1);
        $helper->addToVar(0, 'col3');
        $this->assertEquals(0, $helper->view->col3);
        $helper->addToVar('text', 'col1');
        $this->assertEquals(3, $helper->view->col1);
        $helper->addToVar('3,111', 'col1');
        $this->assertEquals(3114, $helper->view->col1);
    }
}
