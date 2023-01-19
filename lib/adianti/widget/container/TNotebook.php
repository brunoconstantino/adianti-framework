<?php
Namespace Adianti\Widget\Container;

use Gtk;
use GtkNotebook;
use GtkLabel;

/**
 * Notebook
 *
 * @version    2.0
 * @package    widget
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TNotebook extends GtkNotebook
{
    private $contents;
    private $tabAction;
    
    /**
     * Class Constructor
     * @param $width  Notebook's width
     * @param $height Notebook's height
     */
    public function __construct($width = 500, $height = 650)
    {
        parent::__construct();
        parent::set_size_request($width, $height+30);
        parent::connect_after('switch-page', array($this, 'onSwitchPage'));
    }
    
    /**
     * Define if the tabs will be visible or not
     * @param $visible If the tabs will be visible
     */
    public function setTabsVisibility($visible)
    {
        parent::set_show_tabs($visible);
    }
    
    /**
     * Set the notebook size
     * @param $width  Notebook's width
     * @param $height Notebook's height
     */
    public function setSize($width, $height)
    {
        parent::set_size_request($width, $height+30);
    }
    
    /**
     * Returns the Notebook size
     */
    public function getSize()
    {
        return parent::get_size_request();
    }
    
    /**
     * Define the current page to be shown
     * @param $i An integer representing the page number (start at 0)
     */
    public function setCurrentPage($i)
    {
        parent::set_current_page($i);
    }
    
    /**
     * Returns the current page
     */
    public function getCurrentPage()
    {
        return parent::get_current_page();
    }

    /**
     * Return the Page count
     */
    public function getPageCount()
    {
        return parent::get_n_pages();
    }
    
    /**
     * Define the action for the Notebook tab
     * @param $action Action taken when the user
     * clicks over Notebook tab (A TAction object)
     */
    public function setTabAction(TAction $action)
    {
        $this->tabAction = $action;
    }
    
    /**
     * Add a tab to the notebook
     * @param $title   tab's title
     * @param $object  tab's content
     */
    public function appendPage($title, $object)
    {
        if (method_exists($object, 'set_border_width'))
        {
            $object->set_border_width(4);
        }
        parent::append_page($object, new GtkLabel($title));
        $this->contents[] = $object;
    }
    
    /**
     * Switch page event
     */
    public function onSwitchPage()
    {
        if ($this-> window)
        {
            if ($this->tabAction instanceof TAction)
            {
                $param = array('current_page' => $this->getCurrentPage() +1);
                $callback = $this->tabAction->getAction();
                call_user_func($callback, $param);
            }
        }
    }
    
    /**
     * Show the notebook at the screen
     */
    public function show()
    {
        foreach ($this->contents as $content)
        {
            $content->show();
        }
        parent::show();
    }
}
