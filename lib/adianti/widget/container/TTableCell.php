<?php
Namespace Adianti\Widget\Container;

/**
 * TableCell: Represents a cell inside a table
 *
 * @version    2.0
 * @package    widget
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TTableCell
{
    private $content;
    private $properties;
    
    /**
     * Class Constructor
     * @param $content  TableCell content
     */
    public function __construct($content)
    {
        $this->content = $content;
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
     * Returns the cell content
     */
    public function getContent()
    {
        return $this->content;
    }
    
    /**
     * Return the cellproperties
     */
    public function getProperties()
    {
        return $this->properties;
    }
}
