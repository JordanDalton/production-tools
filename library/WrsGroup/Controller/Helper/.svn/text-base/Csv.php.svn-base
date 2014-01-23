<?php
/**
 * Action helper for setting up a CSV file with Zend Framework ContextSwitch
 * action helper
 *
 * @category WrsGroup
 * @package Controller
 * @subpackage Helper
 * @author Eugene Morgan
 */
class WrsGroup_Controller_Helper_Csv extends
    Zend_Controller_Action_Helper_Abstract
{
    /**
     * Sets up a CSV format/context for the given action
     *
     * @param string $action The controller action to add the CSV context for
     * @param string $filename The filename of the resulting CSV file
     */
    public function direct($action, $filename)
    {
        $controller = $this->_actionController;
        $contextSwitch = $controller->getHelper('contextSwitch');

        if (!$contextSwitch->hasContext('csv')) {
            $contextSwitch->addContext('csv', array(
                'suffix' => 'csv',
                'headers' => array(
                    'Content-Type' => 'text/csv',
                    'Expires' => '0',
                    'Cache-Control' =>
                        'must-revalidate, post-check=0, pre-check=0',
                    'Content-Disposition' => 'attachment;filename="' .
                        $filename . '"'
                )
            ));
        }
        $contextSwitch->addActionContext($action, 'csv')
            ->initContext();
    }
}