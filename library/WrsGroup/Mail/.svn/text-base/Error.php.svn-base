<?php
class WrsGroup_Mail_Error extends Zend_Mail
{
    /**
     * @var ArrayObject
     */
    protected $_errors;

    /**
     * The text at the beginning of the subject
     * 
     * @var string
     */
    protected $_subjectPrependText;

    /**
     * @var Zend_View
     */
    public $view;

    /**
     * Sets error data
     * 
     * @param ArrayObject $errors Error data
     */
    public function setErrors($errors)
    {
        $this->_errors = $errors;
        if ($this->view) {
            $this->view->exception = $errors->exception;
            $this->view->request = $errors->request;
        }
    }

    /**
     * Sets subject prepend text
     * 
     * @param string $subjectPrependText The text
     */
    public function setSubjectPrependText($subjectPrependText)
    {
        $this->_subjectPrependText = $subjectPrependText;
    }

    /**
     * Setter for view
     * 
     * @param Zend_View $view 
     */
    public function setView($view)
    {
        $this->view = $view;
    }

    /**
     * Sets the subject and adds any prepended text previously set in 
     * the object
     * 
     * @param string $subjectAppendText The subject text to append
     */
    public function appendSubject($subjectAppendText)
    {
        $this->setSubject($this->_subjectPrependText . $subjectAppendText);
    }

    /**
     * Renders the given view script as the HTML body of the e-mail
     * 
     * @param string $viewScript The path to the view script
     */
    public function setBodyHtmlFromView($viewScript)
    {
        $this->setBodyHtml($this->view->render($viewScript));
    }
}
