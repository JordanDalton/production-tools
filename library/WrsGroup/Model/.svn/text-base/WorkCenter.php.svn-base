<?php
/**
 * Domain object for a work center
 *
 * Designed specifically to represent an entry in the WOWCM table
 * in the WRS Group APlus ERP system.
 * 
 * @category WrsGroup
 * @package Model
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
class WrsGroup_Model_WorkCenter extends WrsGroup_Model_DomainObject_Abstract
{
    protected $_data = array(
        'warehouseId'    => null,
        'workCenterId'   => null,
        'workCenterName' => null,
        'groupId'        => null,

        // Database field names used as object properties in some applications
        // (i.e. RTCS)
        'cwwhid' => null,
        'cwwcid' => null,
        'cwwcds' => null,
        'cwus15' => null,
    );

    /**
     * Based on the WOWCM table
     *
     * @var array
     */ 
    protected $_dbFieldMap = array(
        'warehouseId'    => 'cwwhid',
        'workCenterId'   => 'cwwcid',
        'workCenterName' => 'cwwcds',
        'groupId'        => 'cwus15',   // User field
    );
}
