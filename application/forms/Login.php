<?php
/**
 *  Login
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Jan 26, 2012, 2:08:07 PM
 */
class Application_Form_Login extends Zend_Form
{
    //--------------------------------------------------------------------------
    
    /**
     * Constructor
     */
    public function init()
    {
        // Set the id value for the form.
        $this->setAttrib('id', 'login');
        $this->setName('login');
        
		$userParams = new Zend_Form_Element_Hidden('userParams');
		$userParams->setDecorators(array('ViewHelper'));
        
        // EMAIL ADDRESS
        $usernameInput = new Zend_Form_Element_Text('username');
        $usernameInput->setLabel('Username')
                      ->setAttrib('required', 'required')
                      //->setAttrib('autofocus', 'autofocus')
                      ->setAttrib('autocomplete', 'off')
                      ->setRequired(true)
                      ->addFilter('StripTags')
                      ->addFilter('StringTrim')
                      ->addValidator('NotEmpty');
        
        // PASSWORD
        $passwordInput = new Zend_Form_Element_Password('password');
        $passwordInput->setLabel('Password')
                      ->setAttrib('required', 'required')
                      ->setRequired(true)
                      ->addFilter('StripTags')
                      ->addFilter('StringTrim')
                      ->addValidator('NotEmpty');
               
        // PLACE ALL ELEMENTS INTO AN AN ARRAY
        $elements = array
        (
            $userParams,
            $usernameInput,
            $passwordInput,
        );
        
        /*
         * Loop through all the elements, removing specific decorators.
         */
        foreach($elements as $element)
        {
            $element->removeDecorator('DtDdWrapper');
            $element->removeDecorator('HtmlTag');
            $element->setDecorators(array(
                'ViewHelper',
                'Description',
                array('Label', array('escape' => false)),
                array('HtmlTag', array('tag' => 'div', 'class' => 'clearWrapper'))
            ));
        }
        
        // LOGIN BUTTON
        $loginButton = new Zend_Form_Element_Submit('loginButton');
        $loginButton->removeDecorator('DtDdWrapper')
                    ->removeDecorator('HtmlTag')
                    ->setLabel('Login');
        
        // Add the login button to the list of elements.
        $elements[] = $loginButton;
        
        // PLACE ALL ELEMENTS INTO THE FORM
        $this->addElements($elements);
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Sets parameters for last URL user was on
     *
     * @param string $string The string passed in the URL
     */
    public function setUserParams($string)
    {
        $filter = new Zend_Filter_StripTags();
        $string = $filter->filter($string);
        $this->userParams->setValue($string);
    }
}
/* End of file Login.php */
/* Location: application/forms/Login.php */