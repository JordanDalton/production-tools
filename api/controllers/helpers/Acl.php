<?php
/**
 * Action controller helper for the ACL for default module
 *
 * @package Call Logs
 * @subpackage Controller
 * @author Eugene Morgan
 */
class Controller_Helper_Acl extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Tells whether access is allowed or denied based on user's Active
     * Directory groups
     *
     * @return boolean True if access allowed; false if denied
     */
    public function direct()
    {
        $this->_container   = $this->getActionController()->getInvokeArg('bootstrap')->getContainer();        
        $userRepo           = $this->_container->getComponent('wrsUserRepo');
        $identity           = Zend_Auth::getInstance()->getIdentity();
        $user               = $userRepo->getUser($identity);
                
        // Allow only memberrs of IT and Customer Service
        $allowed =  ($user->isMemberOf('Information Technology')) ? TRUE : FALSE;

        return $allowed;
    }
}