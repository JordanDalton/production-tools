<?php
/**
 *  Bootstrap for REST module.
 *
 *  @author Jordan Dalton <jordandalton@wrsgroup.com>
 *  @created Feb 2, 2012, 3:07:27 PM
 */
class Rest_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initAutoload()
    {
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Molds_',
            'basePath'  => APPLICATION_PATH . '/modules/molds',
            'resourceTypes' => array (
                'form' => array(
                    'path' => 'forms',
                    'namespace' => 'Form',
                ),
                'model' => array(
                    'path' => 'models',
                    'namespace' => 'Model',
                ),
                'service' => array(
                    'path' => 'services',
                    'namespace' => 'Service',
                ),
            )
        ));
        return $autoloader;
    } 
}
/* End of file Bootstrap.php */