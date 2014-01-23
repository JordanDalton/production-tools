<?php

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Assign proper api url based upon the application enviornment
$api_url = (APPLICATION_ENV === 'production') ? 'api.productiontoolsv2' : 'lc.api.productiontoolsv2';

/*
 * Define path to application directory based upon the uri
 * 
 * @author Jordan Dalton <jordandalton@wrsgroup.com>
 */
switch($_SERVER['HTTP_HOST'])
{
  case $api_url:
    define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../api'));
  break;
  default:
    define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
  break;
}

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();