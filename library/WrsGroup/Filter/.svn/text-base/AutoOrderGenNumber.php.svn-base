<?php
/**
 * Filter for adding an order generation number of zero to input if one isn't 
 * supplied
 * 
 * @uses Zend_Filter_Interface
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
class WrsGroup_Filter_AutoOrderGenNumber implements Zend_Filter_Interface
{
    /**
     * filter 
     * 
     * @param mixed $value Value to filter
     * @return string The order number with added generation number if applicable
     * @see Zend_Filter_Interface::filter
     */
    public function filter($value)
    {
        if (strlen($value) === 5) {
            return $value . '00';
        }
        return $value;
    }
}
