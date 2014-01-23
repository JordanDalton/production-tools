<?php
/**
 * Class for forecasts table
 *
 * @category WrsGroup
 * @package Model
 * @subpackage DbTable
 * @author Eugene Morgan
 */
class WrsGroup_Model_DbTable_LgItems_Forecasts extends
    WrsGroup_Db_Table_OdbcDb2_Abstract
{
	protected $_name = 'forecasts';
	protected $_schema = 'lgitems';
    protected $_primary = 'forecast_id';

    /**
     * Gets a simple data row for a forecast
     *
     * @param integer $forecastId OPTIONAL The forecast id; if not provided,
     *  pulls the most recent forecast
     * @return Zend_Db_Table_Row_Abstract A row of data
     */
    public function getForecast($forecastId = null)
    {
        $select = $this->select()
            ->from($this, array(
                'forecast_id',
                'forecast_date'
            ));
        if ($forecastId) {
            $select->where('forecast_id = ?', $forecastId);
        } else {
            $select->order('forecast_id DESC');
        }
        return $this->fetchRow($select);
    }
}
