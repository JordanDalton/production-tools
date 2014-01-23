<?php
/**
 * View helper class for easily formatting an ISO/SQL date 
 * as U.S.-friendly MM/dd/yyyy
 *
 * @uses Zend_View_Helper_Abstract
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
class WrsGroup_View_Helper_DateFormat extends Zend_View_Helper_Abstract
{
    /**
     * Returns the formatted date
     * 
     * @param string $string The string to be formatted
     * @return string The formatted date
     */
    public function dateFormat($string)
    {
        $string = trim($string);

        // Don't do anything to the string if it doesn't match
        // an ISO or SQL-formatted date
        if (!preg_match('/\d{4}-\d{2}-\d{2}/', $string)) {
            return $string;
        }

        return substr($string, 5, 2) . '/'
            . substr($string, 8, 2) . '/'
            . substr($string, 0, 4);
    }
}
