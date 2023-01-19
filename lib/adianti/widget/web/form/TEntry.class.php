<?php
/**
 * Entry Widget (also known as Edit, Input)
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TEntry extends TField
{
    public $id;
    private $mask;
    private $completion;
    
    /**
     * Define the field's mask
     * @param $mask A mask for input data
     */
    public function setMask($mask)
    {
        $this->mask = $mask;
    }
    
    /**
     * Define max length
     * @param  $length Max length
     */
    public function setMaxLength($length)
    {
        $this->tag-> maxlength = $length;
    }
    
    /**
     * Define options for completion
     * @param $options array of options for completion
     */
    function setCompletion($options)
    {
        TPage::include_js('lib/jquery/jquery.autocomplete.js');
        TPage::include_css('lib/jquery/jquery.autocomplete.css');
        
        $this->completion = $options;
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        TPage::include_js('lib/adianti/include/tentry/tentry.js');
        
        // define the tag properties
        $this->tag-> name  = $this->name;    // TAG name
        $this->tag-> value = $this->value;   // TAG value
        $this->tag-> type  = 'text';         // input type
        $this->tag-> style = "width:{$this->size}px";  // size
        
        if ($this->mask)
        {
            $this->tag-> onKeyPress="return entryMask(this,event,'{$this->mask}')";
        }
        
        if ($this->id)
        {
            $this->tag-> id    = $this->id;
        }
        
        // verify if the widget is non-editable
        if (!parent::getEditable())
        {
            $this->tag-> readonly = "1";
            $this->tag->{'class'} = 'tfield_disabled'; // CSS
            $this->tag-> style = "width:{$this->size}px;".
                                 "-moz-user-select:none;";
            $this->tag-> onmouseover = "style.cursor='default'";
        }
        // shows the tag
        $this->tag->show();
        
        if (isset($this->completion))
        {
            $options = json_encode($this->completion);
            $script = new TElement('script');
            $script->add("\$('input[name=\"{$this->name}\"]').autocompleteArray({$options});");
            $script->show();
        }
    }
}
?>