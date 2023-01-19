<?php
/**
 * Text Widget (also known as Memo)
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TText extends TField
{
    protected $size;
    private   $height;
    
    /**
     * Class Constructor
     * @param $name Widet's name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        
        // creates a <textarea> tag
        $this->tag = new TElement('textarea');
        $this->tag->{'class'} = 'tfield';       // CSS
        
        // defines the text default height
        $this->height= 100;
    }
    
    /**
     * Define the widget's size
     * @param  $width   Widget's width
     * @param  $height  Widget's height
     */
    public function setSize($width, $height = NULL)
    {
        $this->size   = $width;
        if ($height)
        {
            $this->height = $height;
        }
    }
    
    /**
     * Show the widget
     */
    public function show()
    {
        $this->tag-> name  = $this->name;   // tag name
        $this->tag-> style = "width:{$this->size}px;";
        if ($this->height)
        {
            $this->tag-> style .=  "height:{$this->height}px";
        }
        
        // check if the field is not editable
        if (!parent::getEditable())
        {
            // make the widget read-only
            $this->tag-> readonly = "1";
            $this->tag->{'class'} = 'tfield_disabled'; // CSS
        }
        
        // add the content to the textarea
        $this->tag->add(htmlspecialchars($this->value));
        // show the tag
        $this->tag->show();
    }
}
?>