<?php
Namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;

use Gtk;
use GtkColorButton;
use GdkColor;

/**
 * Color Widget
 *
 * @version    2.0
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TColor extends TField implements AdiantiWidgetInterface
{
    protected $widget;
    
    public function __construct($name)
    {
        parent::__construct($name);
        
        $this->widget = new GtkColorButton;
        parent::add($this->widget);
        $this->setSize(200);
    }
    
    /**
     * Define the field's value
     * @param $value A string containing the field's value
     */
    public function setValue($content)
    {
        $this->widget->set_color(GdkColor::parse($content));
    }
    
    /**
     * Returns the field's value
     */
    public function getValue()
    {
        $color = $this->widget->get_color();
        $red   = $color->red;
        $green = $color->green;
        $blue  = $color->blue;
        return $this->gdkcolor2rgb($red, $green, $blue);   
    }
    
    /**
     * Define the widget's size
     * @param $size Widget's size in pixels
     */
    public function setSize($width, $height = NULL)
    {
        $this->widget->set_size_request($width, 24);
    }
    
    /**
     * Define a field property
     * @param $name  Property Name
     * @param $value Property Value
     */
    public function setProperty($name, $value, $replace = TRUE)
    {
        if ($name == 'readonly')
        {
            $this->widget->set_editable(false);
        }
    }
    
    /**
     * Return a field property
     * @param $name  Property Name
     * @param $value Property Value
     */
    public function getProperty($name)
    {
        if ($name == 'readonly')
        {
            return $this->widget->get_editable();
        }
    }
    
    /**
     * Converts a gdk color into RGB format
     */
    function gdkcolor2rgb($red, $green, $blue)
    {
        $hex_red   = strtoupper(str_pad(dechex($red   / 65535 * 255), 2, '0', STR_PAD_LEFT));
        $hex_green = strtoupper(str_pad(dechex($green / 65535 * 255), 2, '0', STR_PAD_LEFT));
        $hex_blue  = strtoupper(str_pad(dechex($blue  / 65535 * 255), 2, '0', STR_PAD_LEFT));
        return "#{$hex_red}{$hex_green}{$hex_blue}";
    }
    
    /**
     * Connect object signals
     */
    public function connect_simple($signal, $callback)
    {
        if ($signal !== 'changed')
        {
            $this->widget->connect_simple($signal, $callback);
        }
        else
        {
            $this->widget->connect_simple('color-set', $callback, $this);
        }
    }
}
