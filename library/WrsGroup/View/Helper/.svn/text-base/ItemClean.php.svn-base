<?php
/**
 * View helper class for easily using the ItemClean decorator
 *
 * @uses Zend_View_Helper_Abstract
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
class WrsGroup_View_Helper_ItemClean extends Zend_View_Helper_Abstract
{
    /**
     * Returns the decorated item description
     * 
     * @param WrsGroup_Model_ItemInterface $item An item object
     * @return string The item description
     */
    public function itemClean($item)
    {
        // Yes, this has a hard-coded dependency on the ItemClean decorator
        $decorator = new WrsGroup_Decorator_ItemClean($item);
        return $decorator->getDescription();
    }
}
