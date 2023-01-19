<?php
Namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Form\TField;

use Gtk;
use GtkLabel;

/**
 * Label Widget
 *
 * @version    2.0
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TLabel extends TField implements AdiantiWidgetInterface
{
    private $fontFace;
    private $fontColor;
    private $fontSize;
    private $fontStyle;
    protected $widget;
    public static $counter;
    
    /**
     * Class Constructor
     * @param $value Label's text
     */
    public function __construct($value)
    {
        self::$counter ++;
        parent::__construct('tlabel'.self::$counter);
        $this->widget = new GtkLabel($value);
        $this->widget->set_size_request(-1, -1);
        parent::add($this->widget);
        
        $this->setValue($value);
        $this->set_alignment(0,0.5);
        parent::set_use_markup(TRUE);
    }
    
    /**
     * Define the widget's content
     * @param  $value  widget's content
     */
    public function setValue($value)
    {
        $value = str_replace('<br>',   "\n", $value);
        $value = str_replace('&nbsp;', ' ',  $value);
        
        $this->value = $value;
        parent::set_text($value);
        $this->show();
    }
    
    /**
     * Return the widget's content
     */
    public function getValue()
    {
        return $this->widget->get_text();
    }
    
    /**
     * Define the Field's width
     * @param $width Field's width in pixels
     */
    public function setSize($width, $height = NULL)
    {
        $this->widget->set_size_request($width, -1);
    }
    
    /**
     * Not implemented
     */
    public function setProperty($name, $value, $replace = TRUE)
    {}
    
    /**
     * Not implemented
     */
    public function getProperty($name)
    {}
    
    /**
     * Define the font size
     * @param $size Font size in pixels
     */
    public function setFontSize($size)
    {
        $this->fontSize = $size;
    }
    
    /**
     * Define the font face
     * @param $font Font Family Name
     */
    public function setFontFace($font)
    {
        $this->fontFace = "font-desc='$font $this->fontSize'";
        $this->set_markup($this->getFormattedValue());
    }
    
    /**
     * Define the font color
     * @param $color Font Color
     */
    public function setFontColor($color)
    {
        $this->fontColor = "foreground='$color'";
        $this->set_markup($this->getFormattedValue());
    }
    
    /**
     * Define the style
     * @param $style string "b,i,u"
     */
    public function setFontStyle($style)
    {
        $this->fontStyle = $style;
    }
    
    /**
     * Return the label formatted according to pango
     */
    public function getFormattedValue()
    {
        $value = $this->getValue();
        if ($this->fontStyle)
        {
            $pieces = explode(',', $this->fontStyle);
            if ($pieces)
            {
                $value = $this->value;
                foreach ($pieces as $piece)
                {
                    $value = "<{$piece}>$value</{$piece}>";
                }
            }
        }
        return "<span {$this->fontFace} {$this->fontColor}>$value</span>";
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        $this->set_markup($this->getFormattedValue());
    }
}
