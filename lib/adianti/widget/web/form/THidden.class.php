<?php
/**
 * Hidden field
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class THidden extends TField
{
    /**
     * Show the widget at the screen
     */
    public function show()
    {
        // set the tag properties
        $this->tag-> name  = $this->name;  // tag name
        $this->tag-> value = $this->value; // tag value
        $this->tag-> type  = 'hidden';     // input type
        $this->tag-> style = "width:{$this->size}";
        
        // shows the widget
        $this->tag->show();
    }
}
?>