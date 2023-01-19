<?php
require_once 'init.php';

class TApplication extends AdiantiCoreApplication
{
    protected $content;
    function __construct()
    {
        parent::__construct();
        parent::set_title('Adianti Framework :: Samples');
        $this->content = new GtkFixed;

        $vbox = new GtkVBox;
        $vbox->pack_start(GtkImage::new_from_file('app/images/pageheader.png'), false, false);
        $MenuBar = TMenuBar::newFromXML('menu.xml');
        
        $vbox->pack_start($MenuBar, false, false);
        $vbox->pack_start($this->content, true, true);
        
        parent::add($vbox);
        parent::show_all();
    }
}

$app = new TApplication;

try
{
    Gtk::Main();
}
catch (Exception $e)
{
    $app->destroy();
    new TExceptionView($e);
    Gtk::main();
}
?>
