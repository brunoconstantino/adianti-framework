<?php
/**
 * Label Widget
 *
 * @version    1.0
 * @package    widget_gtk
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TLabel extends GtkLabel
{
    private $wname;
    private $fontFace;
    private $fontColor;
    private $fontSize;
    private $fontStyle;
    
    /**
     * Class Constructor
     * @param $value Label's text
     */
    public function __construct($value)
    {
        parent::__construct();
        parent::set_size_request(-1, -1);
        $this->setValue($value);
        $this->set_alignment(0,0.5);
        parent::set_use_markup(TRUE);
    }
    
    /**
     * Define the widget's name 
     * @param $name Widget's Name
     */
    public function setName($name)
    {
        $this->wname = $name;
    }
    
    /**
     * Returns the name of the widget
     */
    public function getName()
    {
        return $this->wname;
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
    }
    
    /**
     * Return the widget's content
     */
    public function getValue()
    {
        return parent::get_text();
    }
    
    /**
     * Define the widget's size
     * @param $size Widget's size in pixels
     */
    public function setSize($size)
    {
        $this->set_size_request($size,-1);
    }
    
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
    
    /**
     * Define widget properties
     * @param  $property Property's name
     * @param  $value    Property's value
     */
    public function setProperty($property, $value)
    {
        // not applied here
    }
}
?>