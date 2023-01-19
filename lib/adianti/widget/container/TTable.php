<?php
Namespace Adianti\Widget\Container;

use Adianti\Widget\Container\TTableRow;

use Gtk;
use GtkTable;
use GtkHBox;

/**
 * Creates a table layout, with rows and columns
 *
 * @version    2.0
 * @package    widget
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TTable extends GtkTable
{
    private $showed;
    
    /**
     * Class Constructor
     * @param $array Just for Gtk compability reasons
     */
    public function __construct($array=NULL)
    {
        parent::__construct();
        $this->showed = FALSE;
    }
    
    /**
     * Add a new row (TTableRow object) to the table
     * @return TTableRow
     */
    public function addRow()
    {
        $row = new TTableRow;
        $this->rows[] = $row;
        return $row;
    }
    
    /**
     * Add a new row (TTableRow object) with many cells
     * @param $cells Each argument is a row cell
     * @return TTableRow
     */
    public function addRowSet()
    {
        // creates a new Table Row
        $row = $this->addRow();
        
        $args = func_get_args();
        if ($args)
        {
            foreach ($args as $arg)
            {
                if (is_array($arg))
                {
                    call_user_func_array(array($row, 'addMultiCell'), $arg);
                }
                else
                {
                    $row->addCell($arg);
                }
            }
        }
        return $row;
    }
    
    /**
     * Show the table and all aggregated rows
     */
    public function show()
    {
        if ($this->showed === FALSE)
        {
            $i=0;
            if ($this->rows)
            {
                foreach ($this->rows as $row)
                {
                    $c=0;
                    if ($row->getCells())
                    {
                        foreach ($row->getCells() as $column)
                        {
                            $properties = $column->getProperties();
                            $properties['colspan'] = isset($properties['colspan']) ? $properties['colspan'] -1 : 0;
                            $hbox=new GtkHBox;
                            $width  = -1;
                            $height = -1;
                            if (isset($properties['width']))
                            {
                                $width = $properties['width'];
                            }
                            if (isset($properties['height']))
                            {
                                $height = $properties['height'];
                            }
                            $hbox->set_size_request($width, $height);
                            $hbox->set_border_width(1);
                            $hbox->pack_start($column->getContent(), false, false);
                            $column->getContent()->show();
                            //$hbox->pack_start(new GtkHBox, true, true);
                            parent::attach($hbox,$c,$c+1+$properties['colspan'],$i,$i+1, Gtk::FILL, 0, 0, 0);
                            
                            $c++;
                        }
                    }
                    $i++;
                }
            }
            $this->showed = TRUE;
        }
        
        parent::show();
    }
}
