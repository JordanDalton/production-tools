<?php
/**
 * Decorator for expanding the suffix of a mat into a meaningful descriptor
 * 
 * @category WrsGroup
 * @package Model
 * @subpackage Decorator
 */
class WrsGroup_Decorator_MatSuffix extends WrsGroup_Decorator_ItemAbstract
{
    /**
     * @var Zend_Config
     */
    protected $_suffixConfig;

    /**
     * Setter for the config
     * 
     * @param Zend_Config $config A config object
     */
    public function setSuffixConfig($config)
    {
        $this->_suffixConfig = $config;
    }

    /**
     * Constructor
     * 
     * @param WrsGroup_Model_ItemInterface $item An item object
     */
    public function __construct($item)
    {
        parent::__construct($item);

        // Default config
        $this->setSuffixConfig(new Zend_Config(array(
            'AMZ' => 'Amazon',
            'BBBC' => 'Bed Bath & Beyond Canada',
            'BBBCH' => 'Bed Bath & Beyond Commerce Hub',
            'BBBU' => 'Bed Bath & Beyond USA',
            'CA' => 'Canada',
            'CHEFS' => 'Chef\'s Catalog',
            'GV' => 'Gold Violin',
            'HSN' => 'Home Shopping Network',
            'QVC' => 'QVC',
            'R' => 'Retail',
            'SLT' => 'Sur La Table',
            'TBAY' => 'The Bay',
            'THDU' => 'The Home Depot USA',
        )));
    }

    /**
     * This is designed to work with items that are Let's Gel mats.
     *
     * Given the item number, it will attempt to derive a meaningful 
     * descriptor from the suffix of the item number (i.e., '-R' 
     * is for 'Retail').
     * 
     * @return string The meaningful description based on the last part of
     *                the item number
     */
    public function getDescription()
    {
        $suffixArray = $this->_suffixConfig->toArray();

        $split = explode('-', $this->getItemNumber());
        if (count($split) == 1) {
            return '';
        }
        $suffix = array_pop($split);
        if (is_numeric($suffix)) {
            return 'Drop Ship';
        }
        if (isset($suffixArray[$suffix])) {
            return $suffixArray[$suffix];
        }
        return $suffix;
    }
}
