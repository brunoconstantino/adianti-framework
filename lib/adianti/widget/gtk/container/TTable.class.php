<?php
/**
 * Table Container: Allows the developer to organize the widgets according to a table layout, using rows and columns without using borders
 *
 * @version    1.0
 * @package    widget_gtk
 * @subpackage container
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
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
        $row = new TTableRow;;
        $this->rows[] = $row;
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
                            $hbox->set_border_width(1);
                            $hbox->pack_start($column->getContent(), false, false);
                            $column->getContent()->show();
                            //$hbox->pack_start(new GtkHBox, true, true);
                            parent::attach($hbox,$c,$c+1+$properties['colspan'],$i,$i+1, GTK::FILL, 0, 0, 0);
                            
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
?>