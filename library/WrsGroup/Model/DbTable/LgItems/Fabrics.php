<?php
/**
 * Class for fabrics table
 *
 * @category WrsGroup
 * @package Model
 * @subpackage DbTable
 * @author Eugene Morgan
 */
class WrsGroup_Model_DbTable_LgItems_Fabrics
    extends WrsGroup_Db_Table_OdbcDb2_Abstract
{
	protected $_name = 'fabrics';
	protected $_schema = 'lgitems';
    protected $_primary = 'fabric_itno';

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
     * Gets fabrics
     *
     * @return WrsGroup_Model_RecordSet A record set of fabric objects
     */
    public function getFabrics()
    {
        $select = $this->select()
            ->from(array('f' => 'fabrics'), 'fabric_itno')
            ->setIntegrityCheck(false)
            ->joinInner(
                array('s' => 'styles'),
                'f.style_id = s.style_id',
                'style'
            )
            ->joinInner(
                array('sc' => 'styles_colors'),
                'f.color_id = sc.color_id AND f.style_id = sc.style_id',
                'color'
            );
        return $this->fetchAll($select);
    }

    /**
     * Gets fabrics and their child fabric cuts and mat subassemblies with
     * quantity on hand and on P.O. (WRS P.O.)
     *
     * @param string $fabricItno OPTIONAL A fabric item number; if given, only
     *  returns results for that fabric
     * @return Zend_Db_Table_Rowset_Abstract A rowset of data
     */
    public function getFabricsAndChildrenWithOnHandAndPoData($fabricItno = null)
    {
        $select1 = $this->select()
            ->from(
                array('f' => 'fabrics'),
                array(
                    'fabric_itno',
                    new Zend_Db_Expr('f.fabric_itno AS itno'),
                    new Zend_Db_Expr("'' AS mat_itno"),
                    new Zend_Db_Expr("'' AS width"),
                    new Zend_Db_Expr("'' AS height"),
                )
            )
            ->setIntegrityCheck(false)
            ->joinInner(
                array('s' => 'styles'),
                'f.style_id = s.style_id',
                array('style', 'style_id', 'lead_time')
            )
            ->joinInner(
                array('sc' => 'styles_colors'),
                'f.color_id = sc.color_id AND f.style_id = sc.style_id',
                array('color', 'color_id')
            )
            ->joinInner(
                'itbal',
                "f.fabric_itno = ibitno OR CONCAT('Z', f.fabric_itno) = ibitno",
                array(
                    'SUM(ibohq1) AS qty_on_hand',
                    'SUM(ibpoq1) AS qty_on_po',
                    'SUM(ibohq1) AS qty_on_hand_in_yards',
                    'SUM(ibpoq1) AS qty_on_po_in_yards',
                ),
                $this->_config->ibmI->aPlusSchema
            )
            ->group(array(
                'f.fabric_itno',
                'color',
                'style',
                's.style_id',
                'lead_time',
                'sc.color_id'
            ));
        $select2 = $this->select()
            ->from(
                array('fc' => 'fabric_cuts'),
                array(
                    'TRIM(fc.fabric_itno)',
                    new Zend_Db_Expr('fabcut_itno AS itno')
                )
            )
            ->setIntegrityCheck(false)
            ->joinInner(
                array('f' => 'fabrics'),
                'fc.fabric_itno = f.fabric_itno',
                ''
            )
            ->joinInner(
                array('b1' => 'bomco'),
                'b1.bcprit = fabcut_itno AND b1.bccmit = f.fabric_itno',
                '',
                $this->_config->ibmI->aPlusSchema
            )
            ->joinInner(
                array('b2' => 'bomco'),
                'b2.bccmit = fabcut_itno',
                '',
                $this->_config->ibmI->aPlusSchema
            )
            ->joinInner(
                array('m' => 'mats'),
                'b2.bcprit = m.mat_itno',
                array(
                    'mat_itno',
                    'width',
                    'height'
                )
            )
            ->joinInner(
                array('s' => 'styles'),
                'f.style_id = s.style_id',
                array('style', 'style_id', 'lead_time')
            )
            ->joinInner(
                array('sc' => 'styles_colors'),
                'f.color_id = sc.color_id AND f.style_id = sc.style_id',
                array('color', 'color_id')
            )
            ->joinInner(
                'itbal',
                'fc.fabcut_itno = ibitno',
                array(
                    new Zend_Db_Expr('ibohq1 AS qty_on_hand'),
                    new Zend_Db_Expr('ibpoq1 AS qty_on_po'),
                    new Zend_Db_Expr('ibohq1 * b1.bcqtpr AS ' .
                        'qty_on_hand_in_yards'),
                    new Zend_Db_Expr('ibpoq1 * b1.bcqtpr AS ' .
                        'qty_on_po_in_yards'),
                ),
                $this->_config->ibmI->aPlusSchema
            );
        $select3 = $this->select()
            ->from(
                array('m' => 'mats'),
                ''
                // Will add columns from this table to a join below to
                // maintain the correct order for unions
            )
            ->joinInner(
                array('b1' => 'bomco'),
                'b1.bcprit = m.mat_itno',
                '',
                $this->_config->ibmI->aPlusSchema
            )
            ->joinInner(
                array('fc' => 'fabric_cuts'),
                'b1.bccmit = fc.fabcut_itno',
                array(
                    'TRIM(fc.fabric_itno)',
                    new Zend_Db_Expr('mat_itno AS itno'),
                    new Zend_Db_Expr('m.mat_itno'),
                    new Zend_Db_Expr('m.width'),
                    new Zend_Db_Expr('m.height'),
                )
            )
            ->joinInner(
                array('b2' => 'bomco'),
                'b2.bcprit = fc.fabcut_itno AND b2.bccmit = fc.fabric_itno',
                '',
                $this->_config->ibmI->aPlusSchema
            )
            ->joinInner(
                array('s' => 'styles'),
                'm.style_id = s.style_id',
                array('style', 'style_id', 'lead_time')
            )
            ->joinInner(
                array('sc' => 'styles_colors'),
                'm.color_id = sc.color_id AND m.style_id = sc.style_id',
                array('color', 'color_id')
            )
            ->joinInner(
                'itbal',
                'm.mat_itno = ibitno',
                array(
                    new Zend_Db_Expr('ibohq1 AS qty_on_hand'),
                    new Zend_Db_Expr('ibpoq1 AS qty_on_po'),
                    new Zend_Db_Expr('ibohq1 * b1.bcqtpr * b2.bcqtpr ' .
                        'AS qty_on_hand_in_yards'),
                    new Zend_Db_Expr('ibpoq1 * b1.bcqtpr * b2.bcqtpr ' .
                        'AS qty_on_po_in_yards'),
                ),
                $this->_config->ibmI->aPlusSchema
            );
        if ($fabricItno) {
            $select1->where('f.fabric_itno = ?', $fabricItno);
            $select2->where('fc.fabric_itno = ?', $fabricItno);
            $select3->where('fc.fabric_itno = ?', $fabricItno);
        }
        $unionSelect = $this->select()
            ->union(array(
                $select1,
                $select2,
                $select3
            ))
            ->order(array('style_id', 'color_id'));

        return $this->fetchAll($unionSelect);
    }

    /**
     * Gets total count of fabrics and their children in given location(s)
     *
     * @param array $locations A list of locations to search
     * @param string $fabricItno OPTIONAL A fabric item number to narrow search to
     * @return Zend_Db_Table_Rowset_Abstract A rowset of data
     */
    public function getFabricsAndChildrenInLocations($locations, $fabricItno = null)
    {
        $select1 = $this->select()
            ->from(
                array('f' => 'fabrics'),
                array(
                    'fabric_itno',
                    new Zend_Db_Expr('f.fabric_itno AS itno'),
                    new Zend_Db_Expr("'' AS mat_itno"),
                )
            )
            ->setIntegrityCheck(false)
            ->joinInner(
                'wmbal',
                "f.fabric_itno = wbitno OR CONCAT('Z', f.fabric_itno) = wbitno",
                array(
                    'SUM(wbohq1) AS qty_on_hand',
                    'SUM(wbohq1) AS qty_on_hand_in_yards'
                ),
                $this->_config->ibmI->aPlusSchema
            )
            ->where('wbloca in (?)', $locations)
            ->group('f.fabric_itno');
        $select2 = $this->select()
            ->from(
                array('fc' => 'fabric_cuts'),
                array(
                    'TRIM(fc.fabric_itno)',
                    new Zend_Db_Expr('fabcut_itno AS itno')
                )
            )
            ->setIntegrityCheck(false)
            ->joinInner(
                array('f' => 'fabrics'),
                'fc.fabric_itno = f.fabric_itno',
                ''
            )
            ->joinInner(
                array('b1' => 'bomco'),
                'b1.bcprit = fabcut_itno AND b1.bccmit = f.fabric_itno',
                '',
                $this->_config->ibmI->aPlusSchema
            )
            ->joinInner(
                array('b2' => 'bomco'),
                'b2.bccmit = fabcut_itno',
                '',
                $this->_config->ibmI->aPlusSchema
            )
            ->joinInner(
                array('m' => 'mats'),
                'b2.bcprit = m.mat_itno',
                'mat_itno'
            )
            ->joinInner(
                'wmbal',
                'fc.fabcut_itno = wbitno',
                array(
                    'SUM(wbohq1) AS qty_on_hand',
                    'SUM(wbohq1 * b1.bcqtpr) AS qty_on_hand_in_yards'
                ),
                $this->_config->ibmI->aPlusSchema
            )
            ->where('wbloca in (?)', $locations)
            ->group(array(
                'fc.fabric_itno',
                'fc.fabcut_itno',
                'm.mat_itno'
            ));
        $select3 = $this->select()
            ->from(
                array('m' => 'mats'),
                ''
                // Will add columns from this table to a join below to
                // maintain the correct order for unions
            )
            ->joinInner(
                array('b1' => 'bomco'),
                'b1.bcprit = m.mat_itno',
                '',
                $this->_config->ibmI->aPlusSchema
            )
            ->joinInner(
                array('fc' => 'fabric_cuts'),
                'b1.bccmit = fc.fabcut_itno',
                array(
                    'TRIM(fc.fabric_itno)',
                    new Zend_Db_Expr('mat_itno AS itno'),
                    new Zend_Db_Expr('m.mat_itno'),
                )
            )
            ->joinInner(
                array('b2' => 'bomco'),
                'b2.bcprit = fc.fabcut_itno AND b2.bccmit = fc.fabric_itno',
                '',
                $this->_config->ibmI->aPlusSchema
            )
            ->joinInner(
                'wmbal',
                'm.mat_itno = wbitno',
                array(
                    'SUM(wbohq1) AS qty_on_hand',
                    'SUM(wbohq1 * b1.bcqtpr * b2.bcqtpr) AS qty_on_hand_in_yards'
                ),
                $this->_config->ibmI->aPlusSchema
            )
            ->where('wbloca in (?)', $locations)
            ->group(array(
                'fc.fabric_itno',
                'fc.fabcut_itno',
                'm.mat_itno'
            ));
        if ($fabricItno) {
            $select1->where('f.fabric_itno = ?', $fabricItno);
            $select2->where('fc.fabric_itno = ?', $fabricItno);
            $select3->where('fc.fabric_itno = ?', $fabricItno);
        }
        $unionSelect = $this->select()
            ->union(array(
                $select1,
                $select2,
                $select3
            ));
        return $this->fetchAll($unionSelect);
    }

    /**
     * Gets fabrics and children mats shipped from a certain date to now, or
     * optionally, a certain date
     *
     * @param WrsGroup_Date $startDate A date object for the start date
     * @param WrsGroup_Date $endDate OPTIONAL A date object for end date
     * @param string $fabricItno OPTIONAL Narrow results to this fabric item
     *  number and its children
     */
    public function getShippedFabricsAndMats($startDate, $endDate = null, $fabricItno = null)
    {
        $select1 = $this->select()
            ->from(
                array('f' => 'fabrics'),
                array('fabric_itno', new Zend_Db_Expr('f.fabric_itno AS itno'))
            )
            ->setIntegrityCheck(false)
            ->joinInner(
                'iahst',
                'iaitno = f.fabric_itno',
                array(
                    'SUM(iatrqt) AS shipped_this_month',
                    'SUM(iatrqt) AS shipped_this_month_in_yards',
                ),
                $this->_config->ibmI->aPlusSchema
            )
            ->where('iatrcd = ?', 'Z')
            ->where('iatrcc = ?', mb_substr($startDate->toString('yyyy'), 0, 2))
            ->where('iatrdt >= ?', $startDate->toString('yyMMdd'));
        $select1->group('f.fabric_itno');

        $select2 = $this->select()
            ->from(array('m' => 'mats'), '')
                // Will add columns from this table to a join below to
                // maintain the correct order for unions
            ->setIntegrityCheck(false)
            ->joinInner(
                array('b1' => 'bomco'),
                'm.mat_itno = b1.bcprit',
                '',
                $this->_config->ibmI->aPlusSchema
            )
            ->joinInner(
                array('fc' => 'fabric_cuts'),
                'fc.fabcut_itno = b1.bccmit',
                array(
                    'TRIM(fc.fabric_itno) AS fabric_itno',
                    new Zend_Db_Expr('mat_itno AS itno')
                )
            )
            ->joinInner(
                array('b2' => 'bomco'),
                'b2.bccmit = m.mat_itno',
                '',
                $this->_config->ibmI->aPlusSchema
            )
            ->joinInner(
                array('b3' => 'bomco'),
                'b3.bcprit = fc.fabcut_itno AND b3.bccmit = fc.fabric_itno',
                '',
                $this->_config->ibmI->aPlusSchema
            )
            ->joinInner(
                'iahst',
                'iaitno = b2.bcprit',
                array(
                    'SUM(iatrqt * b2.bcqtpr) AS shipped_this_month',
                    'SUM(iatrqt * b1.bcqtpr * b2.bcqtpr * b3.bcqtpr) AS ' .
                        'shipped_this_month_in_yards'
                ),
                $this->_config->ibmI->aPlusSchema
            )
            ->where('b1.bccono = ?', 0)
            ->where('b2.bccono = ?', 0)
            ->where('b3.bccono = ?', 0)
            ->where('iatrcd = ?', 'Z')
            ->where('iatrcc = ?', mb_substr($startDate->toString('yyyy'), 0, 2))
            ->where('iatrdt >= ?', $startDate->toString('yyMMdd'));
        if ($endDate) {
            $select1->where('iatrdt <= ?', $endDate->toString('yyMMdd'));
            $select2->where('iatrdt <= ?', $endDate->toString('yyMMdd'));
        }
        if ($fabricItno) {
            $select1->where('f.fabric_itno = ?', $fabricItno);
            $select2->where('fc.fabric_itno = ?', $fabricItno);
        }
        $select2->group(array(
            'fc.fabric_itno',
            'mat_itno',
        ));
        $unionSelect = $this->select()
            ->union(array($select1, $select2));
        return $this->fetchAll($unionSelect);
    }

    /**
     * Gets totals by fabric item number for fabric being ordered by LG
     *
     * @param string $fabricItno OPTIONAL Fabric item number to narrow results to
     * @return Zend_Db_Table_Rowset_Abstract A rowset of data
     */
    public function getLgFabricShipmentTotals($fabricItno = null)
    {
        $select = $this->select()
            ->from($this, 'fabric_itno')
            ->joinInner(
                array('lsi' => 'lg_shipments_items'),
                'fabric_itno = shipment_itno',
                'SUM(qty_shipped) AS qty_shipped'
            )
            ->joinInner(
                array('ls' => 'lg_shipments'),
                'lsi.shipment_id = ls.shipment_id',
                ''
            )
            ->where('confirmed = ?', 'N')
            ->group('fabric_itno');
        if ($fabricItno) {
            $select->where('fabric_itno = ?', $fabricItno);
        }
        return $this->fetchAll($select);
    }

    /**
     * Gets order header and detail info for open orders for mats derived
     * from the fabrics listed in the fabrics table
     *
     * @param array $fabricCustomers OPTIONAL Customer numbers to restrict
     *  fabric search to (i.e., to skip work orders)
     * @param string $fabricItno OPTIONAL Fabric item number to restrict
     *  fabric and mat search to
     * @return Zend_Db_Table_Rowset_Abstract A rowset of data
     */
    public function getFabricOpenOrders($fabricCustomers = null, $fabricItno = null)
    {
        // SQL expression for grouping orders by month by requested ship date.
        // If the month of the date is the current month or before, returns
        // the first day of the current month; else returns the first day of
        // the month the value represents.
        $dateExpr = new Zend_Db_Expr(
            "CASE " .
            "WHEN ohrsdt <= CONCAT(SUBSTR(REPLACE(CHAR(CURRENT_DATE), '-', ''), 3, 4), '01') " .
            "THEN CONCAT(SUBSTR(CHAR(CURRENT_DATE), 1, 7), '-01') " .
            "ELSE CONCAT(ohrscc, CONCAT(" .
                "CASE " .
                "WHEN LENGTH(TRIM(ohrsdt)) = 5 " .
                "THEN CONCAT('0', CONCAT(LEFT(ohrsdt, 1), CONCAT('-', SUBSTR(ohrsdt, 2, 2)))) " .
                "ELSE CONCAT(LEFT(ohrsdt, 2), CONCAT('-', SUBSTR(ohrsdt, 3, 2))) " .
                "END" .
            ", '-01')) " .
            "END AS order_month"
        );

        // Subselect for open fabric orders. The subselect is used to prevent
        // the need to duplicate complex expressions in the "GROUP BY" clause.
        $subselect1 = $this->select()
            ->from(
                array('f' => 'fabrics'),
                array('fabric_itno')
            )
            ->setIntegrityCheck(false)
            ->joinInner(
                array('s' => 'styles'),
                'f.style_id = s.style_id',
                ''
            )
            ->joinInner(
                array('sc' => 'styles_colors'),
                'f.color_id = sc.color_id AND f.style_id = sc.style_id',
                "CONCAT(color, CONCAT(' ', CONCAT(style, ' Fabric'))) AS description"
            )
            ->joinInner(
                'ordet',
                'fabric_itno = oditno',
                new Zend_Db_Expr('odqtor AS qty_ordered_in_yards'),
                $this->_config->ibmI->aPlusSchema
            )
            ->joinInner(
                'orhed',
                'ohcono = odcono AND ohorno = odorno AND ohorgn = odorgn',
                $dateExpr,
                $this->_config->ibmI->aPlusSchema
            )
            ->where('odortp != ?', 'R')
            ->where('odortp != ?', 'I');
        if ($fabricCustomers) {
            $subselect1->where('ohcsno IN (?)', $fabricCustomers);
        }
        if ($fabricItno) {
            $subselect1->where('fabric_itno = ?', $fabricItno);
        }

        // Select used to group the subselect
        $select1 = $this->select()
            ->from(
                $subselect1,
                array(
                    'fabric_itno',
                    new Zend_Db_Expr('fabric_itno AS order_itno'),
                    'description',
                    'TRIM(CHAR(order_month)) AS order_month',
                    'SUM(qty_ordered_in_yards) AS qty_ordered',
                    'SUM(qty_ordered_in_yards) AS qty_ordered_in_yards',
                )
            )
            ->group(array(
                'fabric_itno',
                'description',
                'TRIM(CHAR(order_month))'
            ));

        // Subselect for open mat orders. The subselect is used to prevent
        // the need to duplicate complex expressions in the "GROUP BY" clause.
        $subselect2 = $this->select()
            ->from(
                array('m' => 'mats'),
                'mat_itno'
            )
            ->setIntegrityCheck(false)
            ->joinInner(
                array('b1' => 'bomco'),
                'b1.bcprit = m.mat_itno',
                '',
                $this->_config->ibmI->aPlusSchema
            )
            ->joinInner(
                array('fc' => 'fabric_cuts'),
                'b1.bccmit = fc.fabcut_itno',
                'TRIM(fc.fabric_itno) AS fabric_itno'
            )
            ->joinInner(
                array('s' => 'styles'),
                'm.style_id = s.style_id',
                ''
            )
            ->joinInner(
                array('sc' => 'styles_colors'),
                'm.color_id = sc.color_id AND m.style_id = sc.style_id',
                "CONCAT(color, CONCAT(' ', CONCAT(style, CONCAT(' ', " .
                    "CONCAT(width, CONCAT(' x ', CONCAT(height, ' Mat'))))))) " .
                    "AS description"
            )
            ->joinInner(
                array('b2' => 'bomco'),
                'b2.bcprit = fabcut_itno AND b2.bccmit = fabric_itno',
                '',
                $this->_config->ibmI->aPlusSchema
            )
            ->joinInner(
                array('b3' => 'bomco'),
                'b3.bccmit = mat_itno',
                '',
                $this->_config->ibmI->aPlusSchema
            )
            ->joinInner(
                'ordet',
                'b3.bcprit = oditno',
                array(
                    new Zend_Db_Expr('odqtor AS qty_ordered'),
                    new Zend_Db_Expr(
                        'odqtor * b1.bcqtpr * b2.bcqtpr * b3.bcqtpr AS qty_ordered_in_yards'
                    ),
                ),
                $this->_config->ibmI->aPlusSchema
            )
            ->joinInner(
                'orhed',
                'ohcono = odcono AND ohorno = odorno AND ohorgn = odorgn',
                $dateExpr,
                $this->_config->ibmI->aPlusSchema
            )
            ->where('b1.bccono = ?', 0)
            ->where('b2.bccono = ?', 0)
            ->where('b3.bccono = ?', 0)
            ->where('odortp != ?', 'R')
            ->where('odortp != ?', 'I');
        if ($fabricItno) {
            $subselect2->where('fabric_itno = ?', $fabricItno);
        }

        // Second select object for mats
        $select2 = $this->select()
            ->from(
                $subselect2,
                array(
                    'fabric_itno',
                    new Zend_Db_Expr('mat_itno AS order_itno'),
                    'description',
                    'TRIM(CHAR(order_month)) AS order_month',
                    'SUM(qty_ordered) AS qty_ordered',
                    'SUM(qty_ordered_in_yards) AS qty_ordered_in_yards',
                )
            )
            ->group(array(
                'fabric_itno',
                'mat_itno',
                'description',
                'TRIM(CHAR(order_month))'
            ));

        // Union select
        $unionSelect = $this->select()
            ->union(array($select1, $select2));
        return $this->fetchAll($unionSelect);
    }
}
