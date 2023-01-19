<?php
Namespace Adianti\Widget\Datagrid;

use Adianti\Control\TAction;

use Gtk;
use GtkTreeViewColumn;
use GtkCellRendererText;
use GtkHBox;
use GtkLabel;
use GtkImage;

/**
 * Representes a DataGrid column
 *
 * @version    2.0
 * @package    widget
 * @subpackage datagrid
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TDataGridColumn extends GtkTreeViewColumn
{
    private $name;
    private $label;
    private $align;
    private $width;
    private $action;
    private $param;
    private $sort_up;
    private $sort_down;
    private $transformer;
    private $renderer;
    private $editaction;
    
    /**
     * Class Constructor
     * @param  $name  = Name of the column in the database
     * @param  $label = Text label that will be shown in the header
     * @param  $align = Column align (left, center, right)
     * @param  $width = Column Width (pixels)
     */
    public function __construct($name, $label, $align, $width = NULL)
    {
        $this->name  = $name;
        $this->label = $label;
        $this->align = $align;
        $this->width = (int) $width;
        
        if ($align == 'left')
            $alignment = 0.0;
        else if ($align == 'center')
            $alignment = 0.5;
        else if ($align == 'right')
            $alignment = 1.0;
        
        parent::__construct();
        $this->renderer = new GtkCellRendererText;
        if ($width)
        {
            $this->renderer->set_property('width', $width);
            parent::set_fixed_width($width);
        }
        $this->renderer->set_property('xalign', $alignment);
        parent::pack_start($this->renderer, true);
        parent::set_alignment($alignment);
        parent::set_title($label);
        
        $header_hbox = new GtkHBox;
        $header_label=new GtkLabel($this->label);
        $header_hbox->pack_start($header_label);
        $this->sort_up   = GtkImage::new_from_stock(GTK::STOCK_GO_UP, Gtk::ICON_SIZE_MENU);
        $this->sort_down = GtkImage::new_from_stock(GTK::STOCK_GO_DOWN, Gtk::ICON_SIZE_MENU);
        $header_hbox->pack_start($this->sort_up);
        $header_hbox->pack_start($this->sort_down);
        
        $header_hbox->show_all();
        
        // hide the ordering images
        $this->sort_up->hide();
        $this->sort_down->hide();
        
        parent::set_widget($header_hbox);
    }
    
    /**
     * Returns the cell renderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }
    
    /**
     * Returns the column's name in the database
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Returns the column's label
     */
    public function getLabel()
    {
        return $this->label;
    }
    
    /**
     * Set the column's label
     * @param $label column label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }
    
    /**
     * Returns the column's align
     */
    public function getAlign()
    {
        return $this->align;
    }
    
    /**
     * Returns the column's width
     */
    public function getWidth()
    {
        return $this->width;
    }
    
    /**
     * Define the action to be executed when
     * the user clicks over the column header
     * @param $action A TAction object
     */
    public function setAction(TAction $action)
    {
        $this->set_clickable(TRUE);
        $this->connect_simple('clicked', array($this, 'onExecuteAction'),
                         $action->getAction(), $action->getParameters());
    }
    
    /**
     * Execute an action
     * @param $action    Callback to be executed
     * @param $parameter User parameters
     * @ignore-autocomplete on
     */
    public function onExecuteAction($action, $parameter)
    {
        call_user_func($action, $parameter);
        if (isset($parameter['order']))
        {
            if ($parameter['order'] == $this->name)
            {
                //$this->sort_up->show(); 
            }
        }
    }
    
    /**
     * Returns the action defined by set_action() method
     * @return the action to be executed when the user clicks over the column header
     */
    public function getAction()
    {
        return $this->action;
    }
    
    /**
     * Define the action to be executed when
     * the user clicks do edit the column
     * @param $action   A TDataGridAction object
     */
    public function setEditAction(TDataGridAction $editaction)
    {
        $this->editaction = $editaction;
    }
    
    /**
     * Returns the action defined by setEditAction() method
     * @return the action to be executed when the
     * user clicks do edit the column
     */
    public function getEditAction()
    {
        // verify if the column has an actions
        if ($this->editaction)
        {
            return $this->editaction;
        }
    }
    
    /**
     * Define a callback function to be applyed over the column's data
     * @param $callback  A function name of a method of an object
     */
    public function setTransformer($callback)
    {
        $this->transformer = $callback;
    }

    /**
     * Returns the callback defined by the setTransformer()
     */
    public function getTransformer()
    {
        return $this->transformer;
    }
}
