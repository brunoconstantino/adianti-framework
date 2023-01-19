<?php
/**
 * Page Controller: Is used as container for all elements inside a page and also as a page controller
 * 
 * @version    1.0
 * @package    widget_gtk
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TPage extends GtkFrame
{
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct();
        parent::set_shadow_type(Gtk::SHADOW_NONE);
    }
    
    /**
     * Executed when the user hits any key
     * @param $widget Source Widget of the event
     * @param $event  Associated GdkEvent
     * @ignore-autocomplete on
     */
    public function onKeyPress($widget, $event)
    {
        if ($event->keyval==Gdk::KEY_Escape)
        {
            parent::hide();
        }
    }
    
    /**
     * Show the page and its child
     */
    public function show()
    {
        $child = parent::get_child();
        if ($child)
        {
            $child->show();
        }
        parent::show_all();
    }
    
    /**
     * Close the current page
     */
    public function close()
    {
        $this->hide();
        return true;
    }
    
    /**
     * Open a File Dialog
     * @param $file File Name
     */
    public function OpenFile($file)
    {
        $ini = parse_ini_file('application.ini');
        $viewer = $ini['viewer'];
        
        if (OS != 'WIN')
        {
            exec("$viewer $file >/dev/null &");
        }
        else
        {
            exec("$viewer $file >NULL &");
        }
    }
}
?>