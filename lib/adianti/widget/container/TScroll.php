<?php
Namespace Adianti\Widget\Container;

use Gtk;
use GtkWidget;
use GtkScrolledWindow;

/**
 * Scrolled Window: Allows to add another containers inside, creating scrollbars when its content is bigger than its visual area
 * 
 * @version    2.0
 * @package    widget
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TScroll extends GtkScrolledWindow
{
    /**
     * Set the scroll size
     * @param  $width   Panel's width
     * @param  $height  Panel's height
     */
    public function setSize($width, $height)
    {
        parent::set_size_request($width, $height);
    }
    
    /**
     * Add a child to the scroll
     * @param  $object A gtk widget 
     */
    function add(GtkWidget $object)
    {
        parent::add_with_viewport($object);
    }
    
    /** 
     * compability reasons
     */
    public function setTransparency($bool)
    {
    }
    
    /**
     * Shows the scroll
     */
    function show()
    {
        parent::show_all();
        $child=parent::get_child()->get_child();
        $child->show();
    }
}
