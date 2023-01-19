<?php
Namespace Adianti\Widget\Container;

use Gtk;
use GtkWidget;
use GtkFrame;

/**
 * Frame Widget: creates a kind of bordered area with a title located at its top-left corner
 *
 * @version    2.0
 * @package    widget
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TFrame extends GtkFrame
{
    /**
     * Constructor method
     * @param $label Frame label
     */
    public function __construct($width = NULL, $height = NULL)
    {
        parent::__construct();
        if ($width AND $height)
        {
            parent::set_size_request($width, $height);
        }
    }
    
    /**
     * Set the frame size
     * @param $width  Width
     * @param $height Height
     */
    public function setSize($width, $height)
    {
        parent::set_size_request($width, $height);
    }
    
    /**
     * Returns the Notebook size
     */
    public function getSize()
    {
        return parent::get_size_request();
    }
    
    /**
     * Set Legend
     * @param  $legend frame legend
     */
    public function setLegend($legend)
    {
        if (is_string($legend))
        {
            parent::set_label($legend);
        }
        else if ($legend instanceof GtkWidget)
        {
            parent::set_label_widget($legend);
        }
    }
    
    /**
     * Show the Frame
     */
    public function show()
    {
        if (parent::get_child())
        {
            // show child object
            parent::get_child()->show();
        }
        parent::show_all();
    }
}
