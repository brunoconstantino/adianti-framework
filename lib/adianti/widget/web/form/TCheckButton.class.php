<?php
/**
 * CheckButton widget
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TCheckButton extends TField
{
    private $indexValue;
    
    /**
     * Define the index value for check button
     * @index Index value
     */
    public function setIndexValue($index)
    {        
        $this->indexValue = $index;
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        // define the tag properties for the checkbutton
        $this->tag-> name  = $this->name;    // tag name
        $this->tag-> type  = 'checkbox';     // input type
        $this->tag-> value = $this->indexValue;   // value
        $this->tag->{'class'} = '';
        
        // compare current value with indexValue
        if ($this->indexValue == $this->value)
        {
            $this->tag-> checked= '1';
        }
        
        // check whether the widget is non-editable
        if (!parent::getEditable())
        {
            // make the widget read-only
            $this->tag-> readonly = "1";
            $this->tag->{'class'} = 'tfield_disabled'; // CSS
        }
        
        // shows the tag
        $this->tag->show();
    }
}
?>