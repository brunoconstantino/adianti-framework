<?php
Namespace Adianti\Control;

use Adianti\Core\AdiantiCoreTranslator;

use Exception;
use Gtk;
use Gdk;
use GtkFrame;

/**
 * Page Controller Pattern: used as container for all elements inside a page and also as a page controller
 *
 * @version    2.0
 * @package    control
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TPage extends GtkFrame
{
    private $constructed;
    
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct();
        parent::set_shadow_type(Gtk::SHADOW_NONE);
        $this->constructed = TRUE;
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
        if (!$this->constructed)
        {
            throw new Exception(AdiantiCoreTranslator::translate('You must call ^1 constructor', __CLASS__ ) );
        }
        
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
    public function openFile($file)
    {
        $ini = parse_ini_file('application.ini');
        $viewer = $ini['viewer'];
        
        if (file_exists($viewer))
        {
            if (OS != 'WIN')
            {
                exec("$viewer $file >/dev/null &");
            }
            else
            {
                $WshShell = new COM("WScript.Shell");
                $WshShell->Run("$file", 0, true);
            }
        }
        else
        {
            throw new Exception(AdiantiCoreTranslator::translate('File not found') . ': ' . $viewer);
        }
    }
}
