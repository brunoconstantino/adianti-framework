<?php
/**
 * Label Widget
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TLabel extends TField
{
    private $fontSize;
    private $fontFace;
    private $fontColor;
    private $fontStyle;
    private $style;
    private $id;
    static private $labelCounter;
    
    /**
     * Class Constructor
     * @param  $value text label
     */
    public function __construct($value)
    {
        $this->id = ++ self::$labelCounter;
        $stylename = 'tlabel'.$this->id;
        
        // set the label's content
        $this->setValue($value);
        
        $this->style = new TStyle($stylename);
        $this->style-> z_index = '1';
        
        // create a new element
        $this->tag = new TElement('label');
        $this->tag->{'class'} = $stylename;
        $this->tag-> onmouseover = "style.cursor='default'";
        
        // set the default property's values
        $this->fontSize  = '10';
        $this->fontFace  = 'sans-serif,arial,verdana';
        $this->fontColor = '#333333';
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
     * Define the style
     * @param $style string "b,i,u"
     */
    public function setFontStyle($style)
    {
        $this->fontStyle = $style;
    }
    
    /**
     * Define the font face
     * @param $font Font Family Name
     */
    public function setFontFace($font)
    {
        $this->fontFace = $font;
    }
    
    /**
     * Define the font color
     * @param $color Font Color
     */
    public function setFontColor($color)
    {
        $this->fontColor = $color;
    }
    
    /**
     * Add an object inside the label
     * @param $obj An Object
     */
    function add($obj)
    {
        $this->tag->add($obj);
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        // define the font style
        $this->style-> font_family         = $this->fontFace;
        $this->style-> color               = $this->fontColor;
        $this->style-> font_size           = $this->fontSize.'pt';
        $this->style-> _moz_user_select    = 'none';
        $this->style-> _webkit_user_select = 'none';
        $this->style-> user_select         = 'none';
        
        // detect a previously loaded style
        if ($loadedstyle = TStyle::findStyle($this->style))
        {
            $this->tag->{'class'} = $loadedstyle;
        }
        else
        {
            $this->style->show();
        }
        
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
            // add content to the tag
            $this->tag->add($value);
        }
        else
        {
            // add content to the tag
            $this->tag->add($this->value);
        }
        
        // show the tag
        $this->tag->show();
    }
}
?>