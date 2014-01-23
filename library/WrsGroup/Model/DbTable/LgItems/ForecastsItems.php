<?php
/**
 * Class for forecasts table
 *
 * @category WrsGroup
 * @package Model
 * @subpackage DbTable
 * @author Eugene Morgan
 */
class WrsGroup_Model_DbTable_LgItems_ForecastsItems 
    extends WrsGroup_Db_Table_OdbcDb2_Abstract
{
	protected $_name = 'forecasts_items';
	protected $_schema = 'lgitems';
    protected $_primary = array('forecast_id', 'forecast_itno', 'month');

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
     * Gets the first and last month covered by the forecast
     *
     * @param integer $forecastId The forecast id
     * @return Zend_Db_Table_Row_Abstract A row of data
     */
    public function getMonthRange($forecastId)
    {
        $select = $this->select()
            ->from($this, array(
                'MIN(month) AS ' . Model_Forecast::FIRST_MONTH,
                'MAX(month) AS ' . Model_Forecast::LAST_MONTH
            ))
            ->setIntegrityCheck(false)
            ->where('forecast_id = ?', $forecastId);
        return $this->fetchRow($select);
    }

    /**
     * Gets forecast data for given forecast id
     *
     * @param integer $forecastId The forecast id
     * @return Zend_Db_Table_Rowset_Abstract A rowset of data
     */
    public function getForecastData($forecastId)
    {
        $fields = array(
            'month',
            'mat_units'
        );
        $select = $this->select()
            ->from($this, $fields)
            ->setIntegrityCheck(false)
            ->joinLeft(
                array('m' => 'mats'),
                'mat_itno = forecast_itno',
                array('mat_itno', 'width', 'height'),
                $this->_schema
            )
            ->joinLeft(
                array('f' => 'fabrics'),
                'fabric_itno = forecast_itno',
                'fabric_itno',
                $this->_schema
            )
            ->joinInner(
                array('s' => 'styles'),
                's.style_id = m.style_id OR s.style_id = f.style_id',
                'style',
                $this->_schema
            )
            ->joinInner(
                array('sc' => 'styles_colors'),
                '(m.color_id = sc.color_id AND m.style_id = sc.style_id) OR ' .
                    '(f.color_id = sc.color_id AND f.style_id = sc.style_id)',
                'color',
                $this->_schema
            )
            ->where('forecast_id = ?', $forecastId)
            ->order(array(
                'sc.style_id',
                'sc.color_id',
                'width',
                'height',
                new Zend_Db_Expr('month')
            ));
        return $this->fetchAll($select);
    }

    /**
     * Gets forecast data in a format useful for calculating fabric outlook
     *
     * @param integer $forecastId The forecast id to retrieve data for
     * @param string $fabricItno OPTIONAL The item number to narrow results to
     * @param float $fabricToMatUnitConversion OPTIONAL An optional conversion
     *  for fabric yards to mat units (used for DAC fabric)
     * @return Zend_Db_Table_Rowset_Abstract A rowset of data
     */
    public function getForecastForFabricOutlook(
        $forecastId,
        $fabricItno = null,
        $fabricToMatUnitConversion = null
    )
    {
        $select1 = $this->select()
            ->from($this, array(
                'month',
                'mat_units',
                'mat_units * b1.bcqtpr * b2.bcqtpr AS yards',
                'forecast_itno'
            ))
            ->setIntegrityCheck(false)
            ->joinInner('mats', 'mat_itno = forecast_itno', '')
            ->joinInner(
                array('b1' => 'bomco'),
                'b1.bcprit = mat_itno',
                '',
                $this->_config->ibmI->aPlusSchema
            )
            ->joinLeft(
                array('b1a' => 'bomco'),
                implode(' AND ', array(
                    'b1.bcprit = b1a.bcprit',
                    'b1.bccmit = b1a.bccmit',
                    'CONCAT(b1.bcefcc, b1.bcefdt) < CONCAT(b1a.bcefcc, b1a.bcefdt)',
                )),
                '',
                $this->_config->ibmI->aPlusSchema
            )
            ->joinInner(
                'fabric_cuts',
                'fabcut_itno = b1.bccmit',
                'TRIM(fabric_itno) AS fabric_itno'
            )
            ->joinInner(
                array('b2' => 'bomco'),
                'b2.bcprit = fabcut_itno AND b2.bccmit = fabric_itno',
                '',
                $this->_config->ibmI->aPlusSchema
            )
            ->joinLeft(
                array('b2a' => 'bomco'),
                implode(' AND ', array(
                    'b2.bcprit = b2a.bcprit',
                    'b2.bccmit = b2a.bccmit',
                    'CONCAT(b2.bcefcc, b2.bcefdt) < CONCAT(b2a.bcefcc, b2a.bcefdt)',
                )),
                '',
                $this->_config->ibmI->aPlusSchema
            )
            ->where('forecast_id = ?', $forecastId)
            ->where('b1a.bcprit IS NULL')
            ->where('b2a.bcprit IS NULL');
        if ($fabricItno) {
            $select1->where('fabric_itno = ?', $fabricItno);
        }
        $select2 = $this->select();
        if ($fabricToMatUnitConversion) {
            $select2->from($this, array(
                'month',
                'mat_units',
                'mat_units * ' . $fabricToMatUnitConversion . ' AS yards',
                'forecast_itno'
            ));
        } else {
            $select2->from($this, array(
                'month',
                'mat_units',
                new Zend_Db_Expr('mat_units AS yards'),
                'forecast_itno'
            ));
        }
        $select2->setIntegrityCheck(false)
            ->joinInner(
                'fabrics',
                'fabric_itno = forecast_itno',
                'TRIM(fabric_itno) AS fabric_itno'
            )
            ->where('forecast_id = ?', $forecastId);
        if ($fabricItno) {
            $select2->where('fabric_itno = ?', $fabricItno);
        }
        $unionSelect = $this->select()
            ->union(array($select1, $select2))
            ->order(array('forecast_itno', 'month'));
        return $this->fetchAll($unionSelect);
    }
}
