<?php
require_once 'init.php';

// define the Home controller for breadcrumb
TXMLBreadCrumb::setHomeController('WelcomeView');

class TApplication extends AdiantiCoreApplication
{
    static public function run($debug = FALSE)
    {
        new TSession;
        if ($_REQUEST)
        {
            parent::run($debug);
        }
    }
}

TApplication::run(TRUE);

