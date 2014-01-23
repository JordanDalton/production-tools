<?php
/**
 * Domain object for BOM
 *
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
class WrsGroup_Model_Bom extends WrsGroup_Model_DomainObject_Abstract
{
    protected $_data = array(
    );

    /**
     * Record set of BOM levels (WrsGroup_Model_BomLevel)
     * 
     * @var WrsGroup_Model_RecordSet
     */
    protected $_bomLevels;

    /**
     * Gets the BOM components for the specified level
     * 
     * @param integer $level The level number
     * @return WrsGroup_Model_RecordSet A record set of BOM components 
     *                                  or null if the level does not exist
     */
    public function getBomComponentsForLevel($level)
    {
        $bomLevel = $this->bomLevels->findOneBy('level', $level); 
        if (!$bomLevel) {
            return null;
        }
        return $bomLevel->getBomComponents();
    }

    /**
     * Gets the BOM routing steps for the specified level
     * 
     * @param integer $level The level number
     * @return WrsGroup_Model_RecordSet A record set of BOM routing steps
     */
    public function getBomRoutingStepsForLevel($level)
    {
        return $this->bomLevels->findOneBy('level', $level)->getBomRoutingSteps();
    }
}
