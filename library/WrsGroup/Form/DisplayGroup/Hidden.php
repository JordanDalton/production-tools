<?php
/**
 * Display group for hidden form elements
 *
 * @category WrsGroup
 * @package Form
 * @subpackage DisplayGroup
 * @author Eugene Morgan
 */
class WrsGroup_Form_DisplayGroup_Hidden extends Zend_Form_DisplayGroup
{
    public function init()
    {
        $this->setAttrib('class', 'hidden');
    }

    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('FormElements')
                 ->addDecorator('Fieldset');
        }
        $this->removeDecorator('HtmlTag');
    }
}
