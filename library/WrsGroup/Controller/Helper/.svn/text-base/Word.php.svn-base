<?php
/**
 * Action helper for setting up a Word file with Zend Framework ContextSwitch
 * action helper
 *
 * @category WrsGroup
 * @package Controller
 * @subpackage Helper
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
class WrsGroup_Controller_Helper_Word extends
    Zend_Controller_Action_Helper_Abstract
{
    /**
     * Sets up a Word format/context for the given action
     *
     * @param string $action The controller action to add the Word context for
     * @param string $filename The filename of the resulting Word file
     */
    public function direct($action, $filename)
    {
        $controller = $this->_actionController;
        $contextSwitch = $controller->getHelper('contextSwitch');

        if (!$contextSwitch->hasContext('word')) {
            $contextSwitch->addContext('word', array(
                'suffix' => 'doc',
                'headers' => array(
                    'Content-Type' => 'application/vnd.ms-word',
                    'Expires' => '0',
                    'Cache-Control' =>
                        'must-revalidate, post-check=0, pre-check=0',
                    'Content-Disposition' => 'attachment;filename="' .
                        $filename . '"'
                )
            ));
        }
        $contextSwitch->addActionContext($action, 'word')
            ->initContext();
    }
}
