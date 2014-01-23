<?php
/**
 * Class for adding the number to a variable and returning it, so that the total 
 * can be output later
 *
 * Primary application would be for displaying a total at the end of a table.
 *
 * @uses Zend_View_Helper_Abstract
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
class WrsGroup_View_Helper_AddToVar extends Zend_View_Helper_Abstract
{
    protected $_index;

    /**
     * Adds the numeric amount of the value to the view variable specified 
     * by $varName, and then echoes the value passed
     *
     * <code>
     * $someVar = 30;
     * echo $this->addToVar(25, 'someVar');   // outputs '25'
     * echo $this->someVar;   // outputs '55'
     * </code>
     * 
     * @param mixed $value A numeric value; strings with commas are OK
     * @param string $varName The view variable name to use for storing the total
     * @return mixed The value that was added to the variable (without commas)
     */
    public function addToVar($value, $varName)
    {
        // Filter any commas in the value
        $value = str_replace(',', '', $value);

        // Set the view variable to zero if it hasn't been initialized.
        if (empty($this->view->$varName)) {
            $this->view->$varName = 0;
        }

        // Don't do anything if the value is zero, empty or non-numeric.
        if (!$value || !is_numeric($value)) {
            return $value;
        }

        $this->view->$varName += $value;
        return $value;
    }
}
