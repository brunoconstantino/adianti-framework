<?php
Namespace Adianti\Widget\Container;

use Gtk;
use GtkVBox;

/**
 * Vertical Box
 *
 * @version    2.0
 * @package    widget
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TVBox extends GtkVBox
{
    /**
     * add a widget
     * @param $widget Widget
     */
    public function add($widget)
    {
        parent::pack_start($widget, FALSE, FALSE);
    }
    
    /**
     * Add a new col with many cells
     * @param $cells Each argument is a row cell
     */
    public function addColSet()
    {
        $args = func_get_args();
        if ($args)
        {
            foreach ($args as $arg)
            {
                $this->add($arg);
            }
        }
    }
    
    /**
     * Static method for pack content
     * @param $cells Each argument is a cell
     */
    public static function pack()
    {
        $box = new self;
        $args = func_get_args();
        if ($args)
        {
            foreach ($args as $arg)
            {
                $box->add($arg);
            }
        }
        return $box;
    }
    
    /**
     * Shows the VBox
     */
    public function show()
    {
        $children = parent::get_children();
        if ($children)
        {
            foreach ($children as $child)
            {
                // show child object
                $child->show();
            }
        }
        parent::show_all();
    }
}
