<?php
/**
 * Password Widget
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TPassword extends TField
{
    /**
     * Show the widget at the screen
     */
    public function show()
    {
        // define the tag properties
        $this->tag-> name  =  $this->name;   // tag name
        $this->tag-> value =  $this->value;  // tag value
        $this->tag-> type  =  'password';    // input type
        $this->tag-> style =  "width:{$this->size}px";
        
        // verify if the field is not editable
        if (!parent::getEditable())
        {
            // make the field read-only
            $this->tag-> readonly = "1";
            $this->tag->{'class'} = 'tfield_disabled'; // CSS
        }
        // show the tag
        $this->tag->show();
    }
}
?>