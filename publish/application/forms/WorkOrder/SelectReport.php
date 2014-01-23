<?php
/**
 *  SelectReport
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Feb 13, 2012, 11:25:15 AM
 */
class Application_Form_WorkOrder_SelectReport extends Zend_Form
{    
    /**
     * Constructor
     */
    public function init()
    {    
        $this->setAttrib('id', 'selectReportForm');
        
        //----------------------------------------------------------------------
        
        $filters = new Zend_Form_Element_MultiCheckbox('selectReportFilters');
        $filters->setDecorators(array(
                     'ViewHelper',
                     array('Description', array('placement' => 'prepend')),
                    array('HtmlTag', array('tag' => 'div', 'class' => 'reportFilters'))
                ))
                ->setDescription('Add Filters')
                ->setMultiOptions(array(
                    'code'           => 'Code',
                    'item_number'    => 'Item Number',
                    'order_number'   => 'Order Number'
                ));
        
        //----------------------------------------------------------------------
        
/*
                       'WO1010RG' => 'WO1010RG (Open Work Order Summary Report)', 
                       'WO1002RG' => 'WO1002RG (Work Order/Back Order)', 
                       'OR1000RG' => 'OR1000RG (Hold Until Complete)'
 */
        
        $reportName = new Zend_Form_Element_Select('selectReportName');
        $reportName->setDecorators(array(
                       'ViewHelper',
                       array('Description', array('placement' => 'prepend'))
                   ))
                   ->setDescription('Select Report')
                   ->setMultiOptions(array(
                       'WO1010RG' => 'WO1010RG (Open Work Order Summary Report)', 
                       'WO1002RG' => 'WO1002RG (Work Order/Back Order)', 
                       'OR1000RG' => 'OR1000RG (Hold Until Complete)'
                   ));
        
        //----------------------------------------------------------------------
        
        $runButton = new Zend_Form_Element_Submit('selectReportRuButton');
        $runButton->setDecorators(array(
                    'ViewHelper',
                    array('HtmlTag', array('tag' => 'div', 'class' => 'boxButtons'))
                  ))
                  ->setLabel('Run Report');
        
        //----------------------------------------------------------------------
		
        // Create index array for potiential work center data to be added to.
		$workCentersArray = array();
        
        // Load the work center model.
		$getWCModel = new Application_Model_WorkCenter();
        
        // Get all the work center records from the api.
		$getWCs = $getWCModel->getWorkCenters();
        
        // If data is set, then proceed..
        if(isSet($getWCs->data))
        {
            // Loop through the results.
            foreach($getWCs->data as $key => $value)
            {
                // Append to the $workCentersArray
                $workCentersArray[$value->work_center_id] = "({$value->work_center_id}) " . $value->work_center_name;
            }   
        }
		
        $workCenter = new Zend_Form_Element_MultiSelect('selectReportWorkCenters');
        $workCenter->setDecorators(array(
					   'ViewHelper',
                       array('Description', array('escape' => false, 'placement' => 'prepend')),
                       array('HtmlTag', array('tag' => 'div', 'id' => 'selectReportWorkCentersContainer'))
					   //array('Label', array('escape' => false))
                   ))
                   ->setDescription('(Optional) Select work center(s). <p class="specialNote"><span>VERY IMPORTANT:</span> Not selecting a work center can cause lengthy query times or page timeouts.')
                   ->setMultiOptions($workCentersArray);       
        
        //----------------------------------------------------------------------

        // Create index array for potiential work order status data to be added to.
        $orderStatusArray = array();
        
        // Load the work order status model.
        $getWOSModel = new Application_Model_WorkOrderStatus();
        
        // Get all the work order statuses from the api.
		$getWOSs = $getWOSModel->getWorkOrderStatus();
        
        // If data is set, then proceed..
        if(isSet($getWOSs->data))
        {
            // Loop through the results.
            foreach($getWOSs->data as $key => $value)
            {
                // Append to the $orderStatusArray
                $orderStatusArray[$key] = $value;
            }   
        }
        
        $orderStatus = new Zend_Form_Element_MultiSelect('selectReportOrderStatuses');
        $orderStatus->setDecorators(array(
                        'ViewHelper',
                        array('Description', array('placement' => 'prepend')),
                        array('HtmlTag', array('tag' => 'div', 'id' => 'selectReportOrderStatusesContainer'))
                    ))
                    ->setDescription('(Optional) Select Order Status(es)')
                    ->setMultiOptions($orderStatusArray);
        
        //----------------------------------------------------------------------
        
        // Place all the elements into one array.
        $elements = array(
            $reportName,
            $workCenter,
            $orderStatus,
            $filters,
            $runButton,
        );
        
        //----------------------------------------------------------------------
        
        // Add all the elements to the form.
        $this->addElements($elements);
    }
    
    //--------------------------------------------------------------------------
}

/* End of file SelectReport.php */