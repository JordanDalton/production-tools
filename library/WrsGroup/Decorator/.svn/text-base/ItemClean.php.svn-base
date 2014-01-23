<?php
/**
 * Decorator for cleanly formatting an item description
 * 
 * @category WrsGroup
 * @package Model
 * @subpackage Decorator
 */
class WrsGroup_Decorator_ItemClean extends WrsGroup_Decorator_ItemAbstract
{
    /**
     * Gets the item description in a clean format.
     * 
     * Spaces are trimmed off end, the two description fields are combined,
     * and logic is used to determine when words are split or joined
     * across the two fields.
     *
     * @return string The item description
     */
    public function getDescription()
    {
        $minUpperCase = 65;
        $maxUpperCase = 90;
        $ordValue = ord(substr($this->_item->getDescription2(), 0, 1));
        if ($ordValue >= $minUpperCase && $ordValue <= $maxUpperCase) {
            return trim($this->_item->getDescription1()) . ' ' . 
                rtrim($this->_item->getDescription2());
        }
        if (strlen(trim($this->_item->getDescription1())) < 
                strlen($this->_item->getDescription1())) {
            return trim($this->_item->getDescription1()) . ' ' . 
                rtrim($this->_item->getDescription2());
        }
        return trim(
            $this->_item->getDescription1()) 
            . rtrim($this->_item->getDescription2()
        );
    }
}
