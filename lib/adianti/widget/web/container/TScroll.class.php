<?php
/**
 * Scrolled Window: Allows to add another containers inside, creating scrollbars when its content is bigger than its visual area
 * 
 * @version    1.0
 * @package    widget_web
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TScroll extends TElement
{
    private $width;
    private $height;
    private $margin;
    private $id;
    private $transparency;
    static private $scrollCounter;    
    
    /**
     * Class Constructor
     */
    public function __construct()
    {
        $this->id = ++ self::$scrollCounter . '_' . uniqid();
        $this->margin = 0;
        $this->transparency = FALSE;
        parent::__construct('div');
    }
    
    /**
     * Set the scroll size
     * @param  $width   Panel's width
     * @param  $height  Panel's height
     */
    public function setSize($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
    }
    
    /**
     * Set the scrolling margin
     * @param  $margin Margin
     */
    public function setMargin($margin)
    {
        $this->margin = $margin;
    }
    
    /** 
     * compability reasons
     */
    public function setTransparency($bool)
    {
        $this->transparency = $bool;
    }
    
    /**
     * Shows the tag
     */
    public function show()
    {
        $stylename = 'tscroll'.$this->id;
        $style = new TStyle($stylename);
        if (!$this->transparency)
        {
            $style-> border        = '1px solid #c2c2c2';
            $style-> background    = '#ffffff';
        }
        $style-> padding_left  = "{$this->margin}px";
        $style-> padding_top   = "{$this->margin}px";
        $style-> padding_right = "{$this->margin}px";
        $style-> padding_bottom= "{$this->margin}px";
        $style-> width         = $this->width.'px';
        $style-> height        = $this->height.'px';
        $style-> overflow      = 'auto';
        $style-> scroll        = 'none';
        
        // check if there's any TSourceCode inside
        if ( is_array($this->children) )
        {
            foreach ($this->children as $child)
            {
                if ($child instanceof TSourceCode)
                {
                    $style-> background_color = '#ffffff';
                }
            }
        }
        // show the style
        $style->show();
        
        $this->{'class'} = $stylename;
        parent::show();
    }
}
?>