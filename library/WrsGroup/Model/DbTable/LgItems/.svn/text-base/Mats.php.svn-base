<?php
/**
 * Class for mats table
 *
 * @category WrsGroup
 * @package Model
 * @subpackage DbTable
 * @author Eugene Morgan
 */
class WrsGroup_Model_DbTable_LgItems_Mats
    extends WrsGroup_Db_Table_OdbcDb2_Abstract
{
	protected $_name = 'mats';
	protected $_schema = 'lgitems';
    protected $_primary = 'mat_itno';

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
     * Gets all mats with mat units per mat
     *
     * @param array $restrictionCodes OPTIONAL Item restriction codes to filter out
     * @return Zend_Db_Table_Rowset_Abstract A rowset of data
     */
    public function getMats($restrictionCodes = null)
    {
        $select = $this->select()
            ->from(
                array('m' => 'mats'),
                array(
                    'mat_itno',
                    'width',
                    'height',
                    'TRIM(forecast_column) AS forecast_column'
                )
            )
            ->setIntegrityCheck(false)
            ->joinInner(
                array('s' => 'styles'),
                's.style_id = m.style_id',
                'style'
            )
            ->joinInner(
                array('sc' => 'styles_colors'),
                'sc.style_id = m.style_id AND sc.color_id = m.color_id',
                'color'
            )
            ->joinInner(
                array('ms' => 'mat_sizes'),
                'm.width = ms.width AND m.height = ms.height',
                array('mat_units', 'size')
            )
            ->order(array('s.style_id', 'sc.color_id', 'width * height'));
        if ($restrictionCodes) {
            $select
                ->joinInner(
                    'itmst',
                    'imitno = m.mat_itno',
                    'TRIM(imrscd) AS restriction_code',
                    $this->_config->ibmI->aPlusSchema
                )
                ->where(
                    'LEFT(imrscd, LENGTH(imrscd) - 3) NOT IN (?)',
                    $restrictionCodes
                );
        }
        return $this->fetchAll($select);
    }

    /**
     * Gets mats shipped from a certain date to now, or optionally,
     * a certain date. IMPORTANT: The result is in mats, not in mat units.
     *
     * @param WrsGroup_Date $startDate A date object for the start date
     * @param WrsGroup_Date $endDate OPTIONAL A date object for end date
     * @return Zend_Db_Table_Rowset_Abstract A rowset of data
     */
    public function getMatsShipped($startDate, $endDate = null)
    {
        $select = $this->select()
            ->from(array('m' => 'mats'), 'mat_itno')
            ->setIntegrityCheck(false)
            ->joinInner(
                'bomco',
                'bccmit = mat_itno',
                '',
                $this->_config->ibmI->aPlusSchema
            )
            ->joinInner(
                'iahst',
                'iaitno = bcprit',
                'SUM(iatrqt) AS qty_shipped',
                $this->_config->ibmI->aPlusSchema
            )
            ->where('bccono = ?', 0)
            ->where('iatrcd = ?', 'Z')
            ->where('iatrdt >= ?', $startDate->toString('yyMMdd'));
        if ($endDate) {
            $select->where('iatrdt <= ?', $endDate->toString('yyMMdd'));
        }
        $select->group('mat_itno');
        return $this->fetchAll($select);
    }

    /**
     * Gets order header and detail info for open orders for mats derived
     * from the SA's listed in the mats table
     *
     * @return Zend_Db_Table_Rowset_Abstract A rowset of data
     */
    public function getMatOpenOrders()
    {
        $select = $this->select()
            ->from($this, 'mat_itno')
            ->setIntegrityCheck(false)
            ->joinInner(
                'bomco',
                'bccmit = mat_itno',
                '',
                $this->_config->ibmI->aPlusSchema
            )
            ->joinInner(
                'ordet',
                'bcprit = oditno',
                array(
                    new Zend_Db_Expr('oditno AS order_itno'),
                    'INTEGER(ROUND(odqtor * bcqtpr, 0)) AS qty_ordered',
                ),
                $this->_config->ibmI->aPlusSchema
            )
            ->joinInner(
                'orhed',
                'ohcono = odcono AND ohorno = odorno AND ohorgn = odorgn',
                array(
                    new Zend_Db_Expr('ohorno AS order_number'),
                    new Zend_Db_Expr('ohorgn AS order_generation_number'),
                    "CONCAT(ohetcc, CONCAT(SUBSTR(ohetdt, 1, 2), " . 
                        "CONCAT('-', CONCAT(SUBSTR(ohetdt, 3, 2), " . 
                        "CONCAT('-', SUBSTR(ohetdt, 5, 2)))))) AS entry_date",
                    "CONCAT(ohrscc, CONCAT(SUBSTR(ohrsdt, 1, 2), " .
                        "CONCAT('-', CONCAT(SUBSTR(ohrsdt, 3, 2), " .
                        "CONCAT('-', SUBSTR(ohrsdt, 5, 2)))))) AS " . 
                        "requested_ship_date",
                    new Zend_Db_Expr('ohhlcd AS hold_code'),
                    new Zend_Db_Expr('ohcsno AS customer_number')
                ),
                $this->_config->ibmI->aPlusSchema
            )
            ->where('odortp != ?', 'R')
            ->where('odortp != ?', 'I')
            ->where('bccono = ?', 0)
            ->order(array('CONCAT(ohetcc, ohetdt)', 'ohorno'));
        return $this->fetchAll($select);
    }
}
