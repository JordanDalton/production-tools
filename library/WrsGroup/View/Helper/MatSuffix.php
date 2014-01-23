<?php
/**
 * View helper class for easily using the MatSuffix decorator
 *
 * @uses Zend_View_Helper_Abstract
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
class WrsGroup_View_Helper_MatSuffix extends Zend_View_Helper_Abstract
{
    /**
     * Returns the decorated item description
     * 
     * @param WrsGroup_Model_ItemInterface $item An item object
     * @return string The item description
     */
    public function matSuffix($item)
    {
        // Yes, this has a hard-coded dependency on the MatSuffix decorator
        $decorator = new WrsGroup_Decorator_MatSuffix($item);
        return $decorator->getDescription();
    }
}
