<?php
/**
 * Html Editor
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class THtmlEditor extends TField
{
    private $widgetId;
    private static $counter;
    protected $size;
    private   $height;
    
    /**
     * Class Constructor
     * @param $name Widet's name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        self::$counter ++;
        $this->widgetId = 'THtmlEditor_'.self::$counter;
        
        // creates a tag
        $this->tag = new TElement('div');
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
        TPage::include_js('lib/jquery/jquery.cleditor.min.js');
        TPage::include_css('lib/jquery/jquery.cleditor.css');

        // check if the field is not editable
        if (parent::getEditable())
        {
            $tag = new TElement('textarea');
            $tag->{'id'} = $this->widgetId;
            $tag->{'class'} = 'thtmleditor';       // CSS
            $tag-> name  = $this->name;   // tag name
            $tag-> style = "width:{$this->size}px;";
            $this->tag->add($tag);
            if ($this->height)
            {
                $tag-> style .=  "height:{$this->height}px";
            }
            
            // add the content to the textarea
            $tag->add(htmlspecialchars($this->value));
            
            $script = new TElement('script');
            $script-> type = 'text/javascript';
            $script->add('
                $("#'.$tag->{'id'}.'").cleditor({width:"'.$this->size.'px", height:"'.$this->height.'px"})
            ');
    		$script->show();
        }
        else
        {
            $this->tag-> style = "width:{$this->size}px;";
            $this->tag-> style.= "height:{$this->height}px;";
            $this->tag-> style.= "background-color:#FFFFFF;";
            $this->tag-> style.= "border: 1px solid #000000;";
            $this->tag-> style.= "padding: 5px;";
            $this->tag-> style.= "overflow: auto;";
            
            // add the content to the textarea
            $this->tag->add($this->value);
        }
        // show the tag
        $this->tag->show();
    }
}
?>