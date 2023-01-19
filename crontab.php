<?php
// define the autoloader
include_once 'lib/adianti/util/TAdiantiLoader.class.php';
spl_autoload_register(array('TAdiantiLoader', 'autoload_web'));

// read configurations
$ini  = parse_ini_file('application.ini');
date_default_timezone_set($ini['timezone']);
TAdiantiCoreTranslator::setLanguage( $ini['language'] );
TApplicationTranslator::setLanguage( $ini['language'] );

// define constants
define('APPLICATION_NAME', $ini['application']);
define('OS', strtoupper(substr(PHP_OS, 0, 3)));
define('PATH', dirname(__FILE__));

try
{
    TTransaction::open('samples');
    $customer = new Customer(1);
    var_dump($customer);
    TTransaction::close();
}
catch (Exception $e)
{
    print $e->getMessage();
}
?>