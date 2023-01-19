<?php
/**
 * RadioButton Widget
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TRadioButton extends TField
{
    private $checked;
   
    /**
     * Show the widget at the screen
     */
    public function show()
    {
        // define the tag properties
        $this->tag-> name  = $this->name;
        $this->tag-> value = $this->value;
        $this->tag-> type  = 'radio';
        $this->tag->{'class'} = '';
        
        // verify if the field is not editable
        if (!parent::getEditable())
        {
            // make the field read-only
            $this->tag-> readonly = "1";
            $this->tag->{'class'} = 'tfield_disabled';
        }
        // show the tag
        $this->tag->show();
    }
}
?>