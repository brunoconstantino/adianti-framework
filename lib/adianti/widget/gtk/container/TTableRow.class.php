<?php
/**
 * Represents a row inside a table
 *
 * @version    1.0
 * @package    widget_gtk
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TTableRow
{
    private $cells;
    
    /**
     * Add a new cell (TTableCell) to the Table Row
     * @param  $value Cell Content
     * @return TTableCell
     */
    public function addCell($content)
    {
        if (is_string($content))
        {
            $content=new GtkLabel($content);
        }
        $cell = new TTableCell($content);
        $this->cells[] = $cell;
        return $cell;
    }
    
    /**
     * Intercepts when user assign value to properties
     * @param $property Property's name
     * @param $value    Property's value
     */
    public function __set($property, $value)
    {
        $this->properties[$property] = $value;
    }
    
    /**
     * Return the Row' cells
     */
    public function getCells()
    {
        return $this->cells;
    }
}
?>