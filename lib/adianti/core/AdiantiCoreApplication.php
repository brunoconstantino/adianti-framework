<?php
Namespace Adianti\Core;

use ReflectionMethod;
use Exception;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Control\TPage;
use Adianti\Widget\Util\TExceptionView;

use Gtk;
use GtkWindow;
use GtkSettings;
use GtkMessageDialog;
use GdkPixbuf;

/**
 * Adianti Core Application
 *
 * @version    2.0
 * @package    core
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class AdiantiCoreApplication extends GtkWindow
{
    const APP_TITLE = 'Adianti Framework';
    static $inst;
    protected $content;
    protected $classname;
    
    /**
     * Constructor Method
     */
    function __construct()
    {
        parent::__construct();
        parent::set_size_request(840,640);
        parent::set_position(GTK::WIN_POS_CENTER);
        parent::connect_simple('delete-event', array($this, 'onClose'));
        parent::connect_simple('destroy', array('Gtk', 'main_quit'));
        parent::set_title(self::APP_TITLE);
        parent::set_icon(GdkPixbuf::new_from_file('favicon.png'));
        
        $gtk = GtkSettings::get_default();
        $gtk->set_long_property("gtk-button-images", TRUE, 0);
        $gtk->set_long_property("gtk-menu-images", TRUE, 0);
        
        self::$inst = $this;
        set_error_handler(array('AdiantiCoreApplication', 'errorHandler'));
    }
    
    /**
     * Pack a class inside the application window
     * @param $callback PHP Callback
     */
    function run($callback)
    {
        if (is_array($callback))
        {
            $class  = $callback[0];
            $method = $callback[1];
        }
        else
        {
            $class = $callback;
        }
        
        if (class_exists($class))
        {
            $object = new $class;
            
            if ($object instanceof TPage)
            {
                if ($children = $this->content->get_children())
                {
                    foreach ($children as $child)
                    {
                        $this->content->remove($child);
                    }
                }
                
                if (isset($method))
                {
                  $object->$method();
                }
                
                $this->content->put($object, 5, 5);
                $object->show( array() );
                return $object;
            }
            else
            {
                $object->show();
                return $object;
            }
        }
        
    }
    
    /**
     * Execute a specific method of a class with parameters
     *
     * @param $class class name
     * @param $method method name
     * @param $parameters array of parameters
     */
    static public function executeMethod($class, $method = NULL, $parameters = NULL)
    {
        if (class_exists($class))
        {
            $inst = self::getInstance();
            $object = $inst->run($class);
            
            if ($method)
            {
                if (method_exists($object, $method))
                {
                    $object->$method($parameters);
                }
            }
        }
    }
    
    /**
     * Load a page
     *
     * @param $class class name
     * @param $method method name
     * @param $parameters array of parameters
     */
    static public function loadPage($class, $method = NULL, $parameters = NULL)
    {
        self::executeMethod($class, $method, $parameters);
    }
    
    /**
     * Goto a page
     *
     * @param $class class name
     * @param $method method name
     * @param $parameters array of parameters
     */
    static public function gotoPage($class, $method = NULL, $parameters = NULL)
    {
        self::executeMethod($class, $method, $parameters);
    }
    
    /**
     * on close the main window
     */
    function onClose()
    {
        $dialog = new GtkMessageDialog(null, Gtk::DIALOG_MODAL, Gtk::MESSAGE_QUESTION,
                                             Gtk::BUTTONS_YES_NO, AdiantiCoreTranslator::translate('Quit the application ?'));
        $dialog->set_position(GTK::WIN_POS_CENTER);
        $response = $dialog->run();
        if ($response == Gtk::RESPONSE_YES)
        {
            $dialog->destroy();
            return false;
        }
        else
        {
            $dialog->destroy();
            return true;
        }
    }
    
    /**
     * Returns the application instance
     */
    static function getInstance()
    {
        return self::$inst;
    }
    
    /**
     * Handle Catchable Errors
     */
    static public function errorHandler($errno, $errstr, $errfile, $errline)
    {
    	if ( $errno === E_RECOVERABLE_ERROR ) { 
    		throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
    	}
     
    	return false;
    }
}
