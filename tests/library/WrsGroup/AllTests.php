<?php
require_once realpath(APPLICATION_PATH . '/../tests/TestSuiteHelper.php');

class Library_WrsGroup_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Library_WrsGroup');
        $suiteHelper = new TestSuiteHelper($suite);
        $suiteHelper->addTestFilesRecursively(dirname(__FILE__));
        return $suite;
    }
}