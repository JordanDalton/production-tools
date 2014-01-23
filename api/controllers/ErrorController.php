<?php

class ErrorController extends Zend_Controller_Action
{
    public function deniedAction()
    {
        // Append layout javascript file
        $this->view->inlineScript()->appendFile('/js/api/layout.js');
    }
    
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        if (!$errors) {
            $this->view->message = 'You have reached the error page';
            return;
        }
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
                break;
        }
        
        // Log exception, if logger available
        if ($log = $this->getLog()) {
            if ($this->getResponse()->getHttpResponseCode() == 500) {
                $trace = '> ' . $errors->exception->getTraceAsString();
                $trace = preg_replace('/\n/', "\n> ", $trace);
                $logContents = $errors->exception->getMessage() . "\n"
                    . $trace . "\n"
                    . str_repeat('-', 75);
                $log->crit($logContents, $errors->exception);
            }
        }
        
        // Conditionally send errors in e-mail
        if ($this->getInvokeArg('displayExceptions') == false) {
            if ($this->getResponse()->getHttpResponseCode() == 500) {
                $container = $this->getInvokeArg('bootstrap')->getContainer();
                $errorMail = $container->getComponent('errorMail');
                $errorMail->setErrors($errors);
                $errorMail->appendSubject($errors->exception->getMessage());
                $errorMail->setBodyHtmlFromView('error/error.phtml');
                $errorMail->send();
            }
        }

        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        
        $this->view->request   = $errors->request;
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }
    
    
    public function accessAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->ViewRenderer->setNoRender(true);
    }
}
