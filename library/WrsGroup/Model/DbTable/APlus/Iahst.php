<?php
/**
 * Class for IAHST table on the IBM i
 *
 * @category WrsGroup
 * @package Model
 * @subpackage DbTable
 * @author Eugene Morgan
 */
class WrsGroup_Model_DbTable_APlus_Iahst
    extends WrsGroup_Db_Table_OdbcDb2_Abstract
{
	protected $_name = 'iahst';
    protected $_primary = array(
        'iaitno',
        'iawhid',
        'iatrcc',
        'iatrdt',
        'iatime',
        'iasq04',
        'iaunms'
    );

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
     * Gets quantity in mat units of Let's Gel fabric derivatives shipped,
     * grouped by fabric, from the date specified onward
     *
     * @param WrsGroup_Date $startDate A date object
     * @return Zend_Db_Table_Rowset_Abstract A rowset of data
     */
    public function getFabricsWithQtyShipped($startDate)
    {
        $bomcoSelect = $this->select()
            ->from(
                'bomco',
                array('bccmit', 'bcprit')
            );
        $select = $this->select()
            ->from(
                $this,
                'ROUND(SUM(iatrqt * b1.bcqtpr * mat_units), 3)'
            )
            ->joinInner(
                array('b1' => 'bomco'),
                'b1.bcprit = iaitno',
                ''
            )
            ->joinInner(
                array('m' => 'mats'),
                'm.mat_itno = b1.bccmit',
                '',
                $this->_config->ibmI->lgItemsSchema
            )
            ->joinInner(
                array('ms' => 'mat_sizes'),
                'ms.width = m.width AND ms.height = m.height',
                '',
                $this->_config->ibmI->lgItemsSchema
            )
            ->joinInner(
                array('b2' => $bomcoSelect),
                'b2.bcprit = m.mat_itno',
                '',
                ''
            )
            ->joinInner(
                array('fc' => 'fabric_cuts'),
                'fc.fabcut_itno = b2.bccmit',
                'fabric_itno',
                $this->_config->ibmI->lgItemsSchema
            )
            ->where('iatrcd = ?', 'Z')
            ->where('iatrdt >= ?', $startDate->toString('yyMMdd'))
            ->group('fabric_itno');
        return $this->fetchAll($select);
    }
}