<?php
/**
 * Action helper for setting up an Excel file with Zend Framework ContextSwitch
 * action helper
 *
 * @category WrsGroup
 * @package Controller
 * @subpackage Helper
 * @author Eugene Morgan
 */
class WrsGroup_Controller_Helper_Excel extends
    Zend_Controller_Action_Helper_Abstract
{
    /**
     * Sets up an Excel format/context for the given action
     *
     * @param string $action The controller action to add the Excel context for
     * @param string $filename The filename of the resulting Excel file
     */
    public function direct($action, $filename)
    {
        $controller = $this->_actionController;
        $contextSwitch = $controller->getHelper('contextSwitch');

        if (!$contextSwitch->hasContext('excel')) {
            $contextSwitch->addContext('excel', array(
                'suffix' => 'xls',
                'headers' => array(
                    'Content-Type' => 'application/vnd.ms-excel',
                    'Expires' => '0',
                    'Cache-Control' =>
                        'must-revalidate, post-check=0, pre-check=0',
                    'Content-Disposition' => 'attachment;filename="' .
                        $filename . '"'
                )
            ));
        }
        $contextSwitch->addActionContext($action, 'excel')
            ->initContext();
    }
}