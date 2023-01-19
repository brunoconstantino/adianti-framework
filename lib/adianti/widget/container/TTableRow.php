<?php
Namespace Adianti\Widget\Container;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Container\THBox;
use Adianti\Widget\Container\TTableCell;

use Exception;
use Gtk;
use GtkLabel;

/**
 * TableRow: Represents a row inside a table
 *
 * @version    2.0
 * @package    widget
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
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
        if (is_null($content))
        {
            throw new Exception(AdiantiCoreTranslator::translate('Method ^1 does not accept null values', __METHOD__));
        }
        else
        {
            if (is_string($content))
            {
                $content=new GtkLabel($content);
            }
            $cell = new TTableCell($content);
            $this->cells[] = $cell;
            return $cell;
        }
    }
    
    /**
     * Add a multi-cell content to a table cell
     * @param $cells Each argument is a row cell
     */
    public function addMultiCell()
    {
        $wrapper = new THBox;
        
        $args = func_get_args();
        if ($args)
        {
            foreach ($args as $arg)
            {
                $wrapper->add($arg);
            }
        }
        
        $this->addCell($wrapper);
    }
    
    /**
     * Clear any child elements
     */
    public function clearChildren()
    {
        $this->cells = array();
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
