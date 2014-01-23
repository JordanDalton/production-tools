<?php
/**
 * Dashboard controller
 * 
 * @author Jordan Dalton <jordandalton@wrsgroup.com>
 */
class DashboardController extends Zend_Controller_Action
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
    
    //--------------------------------------------------------------------------
    
    /**
     * Constructor
     */
    public function init()
    {
        $this->_container = $this->getInvokeArg('bootstrap')->getContainer();
        $this->_config    = $this->_container->getConfig();
        
        // Is user logged in?
        $this->_helper->login();
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
     * Dashboard Homepage
     */
    public function indexAction()
    {
        // Set the page title
        $this->_pageTitle = 'Dashboard';
        
        // Append Javascript File.
        $this->view->inlineScript()->appendFile('/js/gui/dashboard/26350c37c1a148475469c0ad8e3db8a5cca75db2.js');
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Count the number of lines programmed in the application.
     */
    public function projectCountAction()
    {
        $totalLines = 0;
        
        // Create array index for directories
        $dir = array();
        
        $REG_PATH = APPLICATION_PATH;
        $API_PATH = APPLICATION_PATH . '../../api';
        
        // THE API
        $dir[] = "{$API_PATH}/Bootstrap.php";
        $dir[] = "{$API_PATH}/controllers/*";
        $dir[] = "{$API_PATH}/controllers/*/*";
        $dir[] = "{$API_PATH}/forms/*";
        $dir[] = "{$API_PATH}/layouts/*";
        $dir[] = "{$API_PATH}/layouts/*/*";
        $dir[] = "{$API_PATH}/models/*";
        $dir[] = "{$API_PATH}/models/*/*";
        $dir[] = "{$API_PATH}/views/scripts/*/*";
        
        // THE API > REST MODULE
        $dir[] = "{$API_PATH}/modules/rest/controllers/*";
        $dir[] = "{$API_PATH}/modules/rest/models/*/*";
        $dir[] = "{$API_PATH}/modules/rest/models/views/scripts/*/*";
        
        
        $dir[] = "{$REG_PATH}/Bootstrap.php";
        $dir[] = "{$REG_PATH}/controllers/*";
        $dir[] = "{$REG_PATH}/controllers/*/*";
        $dir[] = "{$REG_PATH}/forms/*";
        $dir[] = "{$REG_PATH}/forms/*/*";
        $dir[] = "{$REG_PATH}/layouts/*";
        $dir[] = "{$REG_PATH}/layouts/*/*";
        $dir[] = "{$REG_PATH}/models/*";
        $dir[] = "{$REG_PATH}/models/*/*";
        $dir[] = "{$REG_PATH}/views/scripts/*/*";
        
        
        $dir[] = "{$REG_PATH}/Bootstrap.php";
        $dir[] = "{$REG_PATH}/controllers/*";
        $dir[] = "{$REG_PATH}/controllers/*/*";
        $dir[] = "{$REG_PATH}/forms/*";
        $dir[] = "{$REG_PATH}/forms/*/*";
        $dir[] = "{$REG_PATH}/layouts/*";
        $dir[] = "{$REG_PATH}/layouts/*/*";
        $dir[] = "{$REG_PATH}/models/*";
        $dir[] = "{$REG_PATH}/models/*/*";
        $dir[] = "{$REG_PATH}/views/scripts/*/*";
        
        $dir[] = "{$REG_PATH}/../public/css/*";
        $dir[] = "{$REG_PATH}/../public/css/api/*";
        $dir[] = "{$REG_PATH}/../public/css/api/api-documentation/*";
        $dir[] = "{$REG_PATH}/../public/css/api/auth/*";
        $dir[] = "{$REG_PATH}/../public/css/api/dashboard/*";
        $dir[] = "{$REG_PATH}/../public/css/api/manage-keys/*";
        $dir[] = "{$REG_PATH}/../public/css/gui/*";
        $dir[] = "{$REG_PATH}/../public/css/gui/auth/*";
        $dir[] = "{$REG_PATH}/../public/css/gui/work-center-group/*";
        $dir[] = "{$REG_PATH}/../public/css/gui/work-order/*";
        $dir[] = "{$REG_PATH}/../public/js/*";
        $dir[] = "{$REG_PATH}/../public/js/api/*";
        $dir[] = "{$REG_PATH}/../public/js/api/auth/*";
        $dir[] = "{$REG_PATH}/../public/js/api/index/*";
        $dir[] = "{$REG_PATH}/../public/js/api/manage-keys/*";
        $dir[] = "{$REG_PATH}/../public/js/gui/*";
        $dir[] = "{$REG_PATH}/../public/js/gui/auth/*";
        $dir[] = "{$REG_PATH}/../public/js/gui/dashboard/*";
        $dir[] = "{$REG_PATH}/../public/js/gui/work-center-group/*";
        $dir[] = "{$REG_PATH}/../public/js/gui/work-order/*";
        
        
        // Loop through the directories
        foreach($dir as $directory)
        {
            // Open a known directory, and proceed to read its contents  
            foreach(glob($directory) as $file)  
            {           
                // If a file (not a directory) then proceed to count # of lines.
                if(filetype($file) === 'file')
                {
                    // Count the number of lines in the file.
                    $lines = (count(file($file)) >= 1) ? count(file($file)) : 0;
                    
                    // Add the the 
                    $totalLines = $totalLines + $lines;
                    
                    //echo "[filename: $file ] [filetype: " . filetype($file) . "][{$lines} lines] <br />";  
                    echo "[filename: $file ] [{$lines} lines] <br />";  
                }
            }
        } // end foreach

        // Restrict the number formattting.
        $totalLines = number_format($totalLines,0);
        
        echo("<div style=\"font:normal 25px arial;margin-top:20px\"><div><span style=\"font-weight:bold\">Total Lines:</span> {$totalLines}</div></div>");
        exit;
    }    
}
/* End of file IndexController.php */
/* Location: application/controllers/DashboardController.php */