<?php
/**
 * Repository class for work center groups
 *
 * @author Eugene Morgan <eugenemorgan@wrsgroup.com>
 */
class Application_Model_Repository_WorkCenterGroup
{
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * Constructor
     * 
     * @param Zend_Db_Adapter_Abstract $db An instance of the db adapter
     */
    public function __construct($db)
    {
        $this->_db = $db;
    }

    /**
     * Retrieve all work center groups for given warehouse
     * 
     * @param string $warehouseId       The warehouse ID
     * @return WrsGroup_Model_RecordSet A record set of work center group objects
     */
    public function getAll($warehouseId)
    {
        // Query the database
        $select = $this->_db->select()
            ->from(
                'work_center_group',
                array(
                    'wcg_id',
                    'wcg_name',
                    'wcg_staffing',
                )
            )
            ->join(
                'wowcm',
                'TRIM(cwus15) = wcg_id',
                array(
                    'cwwhid',
                    'cwwcid',
                    'cwwcds',
                )
            )
            ->where('cwwhid = ?', $warehouseId)
            ->order('wcg_id');
        $stmt = $select->query();
        $result = $stmt->fetchAll();

        $workCenterGroups = $this->_groupDbResultForObjectCreation($result);

        return new WrsGroup_Model_RecordSet(
            $workCenterGroups,
            'WrsGroup_Model_WorkCenterGroup'
        );
    }

    /**
     * Gets a work center group, given its ID
     * 
     * @param string $groupId The group ID
     * @return WrsGroup_Model_WorkCenterGroup|null A work center group object
     *                                             or null if none found 
     */
    public function getWorkCenterGroupById($groupId)
    {
        $select = $this->_db->select()
            ->from(
                'work_center_group',
                array(
                    'wcg_id',
                    'wcg_name',
                    'wcg_staffing',
                )
            )
            ->join(
                'wowcm',
                'TRIM(cwus15) = wcg_id',
                array(
                    'cwwhid',
                    'cwwcid',
                    'cwwcds',
                )
            )
            ->where('wcg_id = ?', $groupId);
        $stmt = $select->query();
        $result = $stmt->fetchAll();

        $workCenterGroups = $this->_groupDbResultForObjectCreation($result);

        if (!count($workCenterGroups)) {
            return null;
        }
        return new WrsGroup_Model_WorkCenterGroup(current($workCenterGroups));
    }

    /**
     * Saves a work center group
     * 
     * @param WrsGroup_Model_WorkCenterGroup $workCenterGroup Domain object
     * @param string                         $warehouseId     The warehouse ID
     * @return int The number of rows affected
     */
    public function save($workCenterGroup, $warehouseId)
    {
        // See if the record already exists
        $select = $this->_db->select()
            ->from('work_center_group', array('cnt' => 'COUNT(*)'))
            ->where('wcg_id = ?', $workCenterGroup->groupId);
        $stmt = $select->query();
        $count = $stmt->fetchColumn();

        // Modify work center information
        if ($workCenterGroup->isPopulated('workCenters')) {
            $workCenters = $workCenterGroup->getWorkCenters();
            $workCenterIds = $workCenters->getValues('workCenterId');
            $this->_db->update(
                'wowcm',
                array('cwus15' => $workCenterGroup->groupId),
                array(
                    $this->_db->quoteInto('cwwcid IN (?)', $workCenterIds),
                    $this->_db->quoteInto('cwwhid = ?', $warehouseId),
                )
            );
            $workCenterGroup->unpopulate('workCenters');
        }

        // If it's an update
        if ($count) {
            return $this->_db->update(
                'work_center_group',
                $workCenterGroup->getPopulatedForDb(),
                $this->_db->quoteInto('wcg_id = ?', $workCenterGroup->groupId)
            );
        }

        // If it's an insert
        return $this->_db->insert(
            'work_center_group',
            $workCenterGroup->getPopulatedForDb()
        );
    }

    /**
     * Builds work center group objects from database data
     * 
     * @param array $result A group of data rows
     * @return array A group of work center group objects
     */
    protected function _groupDbResultForObjectCreation($result)
    {
        // Loop through result and create one work center group object
        // for each work center group, with the appropriate work centers
        // as a record set within that object
        $workCenterGroups = array();
        $groupId = '';
        foreach ($result as $row) {
            if ($row['wcg_id'] != $groupId) {
                $workCenterGroups[] = new WrsGroup_Model_WorkCenterGroup($row);
                $groupId = $row['wcg_id'];
                $workCenters[$groupId] = array();
            }
            $workCenters[$groupId][] = new WrsGroup_Model_WorkCenter($row);
        }
        foreach ($workCenterGroups as $key => $workCenterGroup) {
            $workCenterGroup->setWorkCenters(new WrsGroup_Model_RecordSet(
                $workCenters[$workCenterGroup->groupId],
                'WrsGroup_Model_WorkCenter'
            ));
            $workCenterGroups[$key] = $workCenterGroup;
        }
        return $workCenterGroups;
    }

    /**
     * Deletes a work center group
     * 
     * @param integer $groupId The work center group ID
     * @return integer Number of rows affected
     */
    public function deleteWorkCenterGroup($groupId)
    {
        // First, scrub any work center records associated with the work
        // center group
        $this->_db->update(
            'wowcm',
            array('cwus15' => ''),
            $this->_db->quoteInto('cwus15 = ?', $groupId)
        );

        return $this->_db->delete(
            'work_center_group', 
            $this->_db->quoteInto('wcg_id = ?', $groupId)
        );
    }
}
