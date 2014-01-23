<?php
/**
 * Class for fabric cuts table
 *
 * @category WrsGroup
 * @package Model
 * @subpackage DbTable
 * @author Eugene Morgan
 */
class WrsGroup_Model_DbTable_LgItems_FabricCuts
    extends WrsGroup_Db_Table_OdbcDb2_Abstract
{
	protected $_name = 'fabric_cuts';
	protected $_schema = 'lgitems';
    protected $_primary = 'fabcut_itno';

    /**
     * @var Zend_Config
     */
    protected $_config;

    /**
     * Sets the config object
     *
     * @param Zend_Config $config The config object
     */
    public function setConfig($config)
    {
        $this->_config = $config;
    }

    /**
     * Gets a select object for fabric cuts that are used to produce mats
     * of the given mat unit size
     *
     * @param float $matUnits Number of mat units
     * @return WrsGroup_Db_Table_OdbcDb2_Select A select object
     */
    public function selectFabricCutsForMatUnitSize($matUnits)
    {
        $select = $this->select()
            ->from(array('fc' => 'fabric_cuts'), 'fabcut_itno')
            ->joinInner(
                'bomco',
                'bccmit = fabcut_itno',
                '',
                $this->_config->ibmI->aPlusSchema
            )
            ->joinInner(
                array('m' => 'mats'),
                'bcprit = mat_itno',
                ''
            )
            ->joinInner(
                array('ms' => 'mat_sizes'),
                'm.width = ms.width AND m.height = ms.height',
                ''
            )
            ->where('mat_units = ?', $matUnits);
        return $select;
    }

    /**
     * Gets fabric cuts with matching mats in mats table and APlus Bomco table
     * along with the number of mat units in each fabric cut
     *
     * @return Zend_Db_Table_Rowset_Abstract Rowset of data
     */
    public function getFabricCutsWithMatUnits()
    {
        $select = $this->select()
            ->from($this, array(
                'fabcut_itno',
                'TRIM(fabric_itno) AS fabric_itno'
            ))
            ->setIntegrityCheck(false)
            ->joinInner(
                'bomco',
                'bccmit = fabcut_itno',
                '',
                $this->_config->ibmI->aPlusSchema
            )
            ->joinInner(
                array('m' => 'mats'),
                'bcprit = m.mat_itno',
                'mat_itno'
            )
            ->joinInner(
                array('ms' => 'mat_sizes'),
                'ms.width = m.width AND ms.height = m.height',
                'ROUND(mat_units/bcqtpr, 2) AS mat_units_per_fabric_cut'
            );
        return $this->fetchAll($select);
    }
}
