<?php
/**
 *  WorkOrderController
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Feb 10, 2012, 8:36:49 AM
 */
class WorkOrderController extends Zend_Controller_Action
{
    /**
     * @var Zend_Config
     */
    protected $_config;
    /**
     * @var Yadif_Container
     */
    protected $_container;
    /**
     * Set the page title.
     * 
     * @var string
     */
    protected $_pageTitle = 'Untitled';
	/**
	 * @mixed The URI parameters
	 */
    private $_params;
    
    //--------------------------------------------------------------------------
    
    /**
     * Constructor
     */
    public function init()
    {
        $this->_container = $this->getInvokeArg('bootstrap')->getContainer();
        $this->_config    = $this->_container->getConfig();
        $this->_db        = $this->_container->getComponent('db');
        $this->_redirector= $this->_helper->getHelper('Redirector');
        $this->_redirector->setExit(true);
        
		// Create instance of httpClient from the object.ini file.
        $this->_httpClient = $this->_container->httpClient;
        
        // Get the parameters from the URI
        $this->_params = $this->_request->getParams();
        
        // Timeout after 5 minutes
        set_time_limit(300);
    }

    //--------------------------------------------------------------------------
    
    /**
     * Run before an action is dispatched.
     */
    public function preDispatch()
    {
        // Is user logged in?
        $this->_helper->login();

        // Is user allowed?
        $allowed = $this->_helper->Acl();

        // Redirect if they do not have permissions
        if(!$allowed) $this->_redirector->goToSimple('denied', 'error', 'default');
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Post Dispatch
     */
    public function postDispatch()
    {
        // Set the page title
        $this->view->headTitle()->prepend($this->_pageTitle);

    }
    
    //--------------------------------------------------------------------------

    /**
     * Work Order Dashboard
     */
    public function indexAction()
    {    
        // Check if postback
        if($this->_request->isPost())
        {
            // Get the postback data.
            $postData = $this->_request->getPost();
            
            // Load the work order report model
            $model = new Application_Model_WorkOrderReport($this->_db);

            // Create index array for the headers
            $headers = array();

            // Create index array for the rows
            $rows = array();
            
            // Set the report name
            $report = $postData['report'];
            
            // Decode the post data
            $decoded = Zend_Json::decode(urldecode($postData['postData']), Zend_Json::TYPE_OBJECT);

            // Set the misc data
            $misc = array(
                'bo_value'           => $postData['bo_value'],
                'record_count'       => $postData['record_count'],
                'work_centers'       => (strlen($postData['work_centers']) >= 1) ? $postData['work_centers'] : null,
                'work_center_groups' => (strlen($postData['work_center_groups']) >= 1) ? $postData['work_center_groups'] : null
            );
            
            // Loop through the array.
            foreach($decoded as $decode)
            {
                //$stripOpeningBracket = preg_replace('/(^\[)/', '$replacement', $subject);

                // Create index array to store row data to.
                $rowData = array();

                foreach($decode as $key => $value)
                {
                    // Trim the $key
                    $key = trim($key);

                    // Trim the $value
                    $value = trim($value);

                    /* 
                     * If the $key does not currently exists in our list of headers
                     * then add it.
                     */
                    if(!in_array($key, $headers)) $headers[] = $key;

                    // Create key and values for each column of data.
                    $rowData[$key] = $value; //addcslashes($value, '\"');
                }

                // Append our $rowData to the $rows array
                $rows[] = $rowData;
            }

            // Now lets save the data
            $save = $model->saveTableReport($report, $headers, $rows, $misc);
            
            // FORWARD TO DASHBOARD
            $this->_redirector->gotoSimple($save ? 'save-successful' : 'save-failed', 'work-order', '');
        }
        
        // Load form
        $selectReportForm   = new Application_Form_WorkOrder_SelectReport();

        // If param is set, include rest request
        if(isSet($this->_params['run']))
        {
            // Create new instance of our Zend_Rest_Client
            $restClient = new My_Rest_Client();

            // Submit request for the report from the REST API SERVER
            $restClientResults = $restClient->execute('Get','rest/report/', $this->_params);

            // Set the table data with results from the rest client.
            $tableData = Zend_Json::decode($restClientResults, Zend_Json::TYPE_OBJECT); 
        }
        
        /**************************** PREPARE THE VIEW ************************/
        
        // Pass the URI paramters to the view
        $this->view->params = $this->_params;
        
        // Set the page title
        $this->_pageTitle = 'Work Orders';
        
        // Pass the form to the view.
        $this->view->selectReportForm = $selectReportForm;
        
        // Pass the $tableData to the view
        $this->view->tableData = isSet($this->_params['run']) ? $tableData : false;
        
        // Append Stylesheet
        $this->view->headLink()->appendStylesheet('/css/gui/work-order/workOrderIndexAction.css', 'all');
        
        // Append Table Sorter Javascript File.
        $this->view->inlineScript()->appendFile('/js/libs/jquery.tablesorter.js');
        
        // Append Table-2-Json Javascript File.
        $this->view->inlineScript()->appendFile('/js/libs/tabletojson.js');
        
        // Append Javascript File.
        $this->view->inlineScript()->appendFile('/js/gui/work-order/workOrderIndexAction.js');
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Archived Work Order Reports Page
     */
    public function archivesAction()
    {
        // Load the the work order report model.
        $model = new Application_Model_WorkOrderReport($this->_db);
        
        // Query for the last 2 weeks of work order table reports.
        $getResults = $model->getTwoWeeksOfTableReports();
        
        // Create index array for row data to be applied to.
        $finalResults = array();
        
        // Create instance of Zend_Date();
        $zd = new Zend_Date();
        
        if($getResults)
        {
            // Loop through the data
            foreach($getResults as $result)
            {
                // Convert $result to object array.
                $result = (object) $result;

                // Conver the created time to a formatted date.
                $result->created = $zd->set($result->created)->toString('MM/dd/YYYY @ hh:mm a');

                // Append the row of results to the $getResults array.
                $finalResults[] = $result;
            }
        }
        
        /**************************** PREPARE THE VIEW ************************/
        
        // Set the page title
        $this->_pageTitle = 'Work Order Reports Archive';
        
        // Set the results for the view
        $this->view->results = $finalResults;
    }
        
    //--------------------------------------------------------------------------
    
    public function archivedRecordAction()
    {
        // Load the the work order report model.
        $model = new Application_Model_WorkOrderReport($this->_db);
        
        // Query for the work order table report.
        $getResults = $model->getTableReport($this->_params['id']);
        
        // Create index array for table data to be applied to.
        $tableData = array();

        // Create instance of Zend_Date();
        $zd = new Zend_Date();
        
        // Loop through the data
        foreach($getResults as $result)
        {
            // Convert $result to object array.
            $result = (object) $result;
            
            // Conver the created time to a formatted date.
            $result->created = $zd->set($result->created)->toString('MM/dd/YYYY @ hh:mm a');
            
            // Decode the headings
            $result->headings = unserialize($result->headings);        
            
            // Decode the rows
            $result->rows = unserialize($result->rows);
            
            /**\/
            print('<pre>');
            print($result->rows);
            print('</pre>');
            exit;
            /**/
            
            // Append the row of results to the $tableData array.
            $tableData[] = $result;
        }
        
        /**\/
        print('<pre>');
        print_r($tableData);
        print('</pre>');
        exit;
        /**/
        
        /**************************** PREPARE THE VIEW ************************/
        
        // Set the page title
        $this->_pageTitle = 'Viewing Archived Record';
        
        // Set the table data for the view
        $this->view->tableData = $tableData;   
        
        // Append Stylesheet
        $this->view->headLink()->appendStylesheet('/css/gui/work-order/workOrderArchivedRecordAction.css', 'all');
        
        // Append Javascript File.
        $this->view->inlineScript()->appendFile('/js/gui/work-order/workOrderArchivedRecordAction.js');
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Saving Report : Successful
     */
    public function saveSuccessfulAction()
    {
        /**************************** PREPARE THE VIEW ************************/
        
        // Set the page title.
        $this->_pageTitle = 'Report Saved Successfully';
    }
    
    //--------------------------------------------------------------------------

    /**
     * Saving Report : Failed!!
     */
    public function saveFailedAction()
    {
        /**************************** PREPARE THE VIEW ************************/
        
        // Set the page title.
        $this->_pageTitle = 'Report Failed To Save';
    }
    
    //--------------------------------------------------------------------------
}
/* End of file WorkOrderController.php */
/* Location: application/controllers/WorkOrderController.php */