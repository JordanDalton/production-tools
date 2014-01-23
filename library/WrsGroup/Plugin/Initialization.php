<?php
/**
 * Controller plugin for Zend Framework; performs a few functions upon
 * initializing an application.
 *
 * @package WrsGroup
 * @subpackage Plugin
 * @author Eugene Morgan
 */
class WrsGroup_Plugin_Initialization extends Zend_Controller_Plugin_Abstract
{
	/**
     * @var string Name of the module
     */
    private $_module;

    /**
     * @var Zend_Config_Ini Config object for the current module
     */
    private $_config;

    /**
     * Using the method dispatchLoopStartup() means that this will be run
     * every time a request is made. It will not be run again for a forward;
     * to do that, use preDispatch()
     *
     * @param Zend_Controller_Request_Abstract $request The request
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
	{
		$this->_module = $request->getModuleName();
        $this->_loadConfigFile();
        $this->_setModuleLayout();
	}

    /**
     * Loads config file specific to this module, if it exists
     *
     */
    private function _loadConfigFile()
    {
        $path = APP_ROOT . '/application/modules/' . $this->_module .
            '/config/config.ini';
        if (file_exists($path)) {
            $this->_config = new Zend_Config_Ini($path);
            $moduleParts = explode('-', $this->_module);
            foreach ($moduleParts as $part) {
                if (empty($moduleName)) {
                    $moduleName = $part;
                } else {
                    $moduleName .= ucfirst($part);
                }
            }
            Zend_Registry::set($moduleName . 'Config', $this->_config);
        }
    }

    /**
     * Sets default layout for module, if applicable
     *
     */
    private function _setModuleLayout()
    {
        if (!$this->_config) {
            return;
        }

        if (!$this->_config->app->useModuleLayout) {
            return;
        }

        $layout = Zend_Layout::getMvcInstance();
        $path = APP_ROOT . '/application/modules/' . $this->_module . '/layouts';
        if ($layout) {
            $layout->setLayoutPath($path);
        } else {
            $options = array('layoutPath' => $path);
            Zend_Layout::startMvc($options);
        }
    }
}
