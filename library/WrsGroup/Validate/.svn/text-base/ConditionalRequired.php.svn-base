<?php
/**
 * Class to validate that if a parent field is filled in or selected, then
 * the child field is required.
 * *Note*: The child field must have allowEmpty set to false.
 *
 * @package WrsGroup
 * @subpackage Validate
 * @author Eugene Morgan
 */
class WrsGroup_Validate_ConditionalRequired extends Zend_Validate_Abstract
{
    const REQUIRED = 'required';
    
    protected $_messageTemplates = array(
        self::REQUIRED => 'Based on your answer above, this field is required.'
    );
    
    /**
     * @var Zend_Form
     */
    protected $_form;
    protected $_parentField;
    protected $_parentValue;

    /**
     * Constructor; sets validator options
     *
     * @param Zend_Form $form A form object
     * @param string $parentField 'Parent' field name - if it's checked, the
     *     child field is required
     * @param mixed $parentValue The value the parent field needs to be in order
     *     for the child field to be required; default is null which means any
     *     non-empty value is acceptable
     */
    public function __construct($form, $parentField, $parentValue = null)
    {
        $this->_form = $form;
        $this->_parentField = $parentField;
        $this->_parentValue = $parentValue;
    }
    
    /**
     * (non-PHPdoc)
     * @see library/Zend/Validate/Zend_Validate_Interface#isValid()
     */
    public function isValid($value)
    {
        if ($parentValue = $this->_form->getValue($this->_parentField)) {
            $validator = new Zend_Validate_NotEmpty();
            
            // If any value is ok for the parent field, return true if the
            // parent field is empty
            if (null === $this->_parentValue) {
                if (!$validator->isValid($parentValue)) {
                    return true;
                }
                
            // If a specific value needs to be in the parent field, and the
            // parent field has a different value, return true
            } elseif ($this->_parentValue != $parentValue) {
                return true;
            }
            
            // At this point, we need to make sure the child field is not empty
            if (!$validator->isValid($value)) {
                $this->_error(self::REQUIRED);
                return false;
            }
        }
        return true;
    }
}