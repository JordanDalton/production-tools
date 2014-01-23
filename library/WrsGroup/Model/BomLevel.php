<?php
/**
 * Domain object for BOM level
 *
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
class WrsGroup_Model_BomLevel extends WrsGroup_Model_DomainObject_Abstract
{
    protected $_data = array(
        'level' => null,   // Level number
    );

    /**
     * Parent BOM item
     *
     * @var WrsGroup_Model_BomParent
     */
    protected $_bomParent;

    /**
     * Record set of BOM components (WrsGroup_Model_BomComponent)
     *
     * @var WrsGroup_Model_RecordSet
     */
    protected $_bomComponents;

    /**
     * Record set of routing steps (labor) at this level 
     * (WrsGroup_Model_BomRoutingStep)
     *
     * @var WrsGroup_Model_RecordSet
     */
    protected $_bomRoutingSteps;
}
