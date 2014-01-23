<?php
/**
 * @author Eugene Morgan
 */
class Controller_Helper_Login extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Checks if user is logged in, and if not, redirects to login page
     *
     */
    public function direct()
    {
        if (!Zend_Auth::getInstance()->hasIdentity())
        {
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            
            $params = $this->getRequest()->getParams();
            
            $string = WrsGroup_RequestUtils::stringifyParams($params);
            
            $redirector->gotoSimple('index', 'login', 'auth', array('userParams' => $string));
        }
    }
}