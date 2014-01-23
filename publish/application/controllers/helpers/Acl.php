<?php
/**
 * Action controller helper for the ACL for call logs module
 *
 * @package Call Logs
 * @subpackage Controller
 * @author Eugene Morgan (Modified by Jordan Dalton)
 */
class Controller_Helper_Acl extends Zend_Controller_Action_Helper_Abstract
{
    //--------------------------------------------------------------------------
    
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
                
        // Allow only members of particular groups.
        $allowed =  (
            $user->isMemberOf('Information Technology') || 
            $user->isMemberOf('Production Tools Application')
        ) ? TRUE : FALSE;

        return $allowed;
    }
    
    //--------------------------------------------------------------------------
}
/* End of file Acl.php */
/* Location: api/modules/rest/controllers/helpers/Acl.php */