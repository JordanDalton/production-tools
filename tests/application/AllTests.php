<?php
require_once realpath(APPLICATION_PATH . '/../tests/TestSuiteHelper.php');

class Application_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Application');
        $suiteHelper = new TestSuiteHelper($suite);
        $suiteHelper->addTestFilesRecursively(dirname(__FILE__));
        return $suite;
    }
}
