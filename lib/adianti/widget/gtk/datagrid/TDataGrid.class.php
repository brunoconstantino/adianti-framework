<?php
/**
 * DataGrid Widget: Allows to create datagrids with rows, columns and actions 
 *
 * @version    1.0
 * @package    widget_gtk
 * @subpackage datagrid
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TDataGrid extends GtkVBox
{
    private $count;
    private $columns;
    private $types;
    private $width;
    private $action_area;
    private $pageNavigation;
    
    /**
     * Class Constructor
     */
    public function __construct()
    {
        $this->types   = array();
        $this->columns = array();
        $this->count   = 0;
        
        parent::__construct();
        $this->view  = new GtkTreeView;
        $this->model = new GtkListStore;
        $this->view->set_size_request(200,-1);
        $scroll = new GtkScrolledWindow;
        $scroll->add($this->view);
        
        $this->view->connect_simple('row-activated', array($this, 'onDoubleClick'));
        
        $this->action_area = new GtkHButtonBox;
        $this->action_area->set_layout(Gtk::BUTTONBOX_START);
        parent::pack_start($scroll, true, true);
        parent::pack_start($this->action_area, false, false);
        parent::set_size_request(-1,200);
    }
    
    /**
     * Define the Height
     * @param $height An integer containing the height
     */
    public function setHeight($height)
    {
        parent::set_size_request(-1,$height);
    }
    
    /**
     * Add a Column to the DataGrid
     * @param $object A TDataGridColumn object
     */
    public function addColumn(TDataGridColumn $column)
    {
        $renderers = $column->get_cell_renderers();
        $cell_renderer = $renderers[0];
        $column->add_attribute($cell_renderer, 'text', $this->count);
        $column->set_cell_data_func($cell_renderer, array($this,'formatZebra'));
        $this->view->append_column($column);
        $this->types[] = GObject::TYPE_STRING;
        $this->columns[] = $column;
        if ($column->getWidth())
        {
            $this->width += $column->getWidth();
            $width = $this->width + count($this->columns)*10;
            $this->view->set_size_request($width, -1);
        }
        $this->count ++;
    }
    
    /**
     * Add an Action to the DataGrid
     * @param $object A TDataGridAction object
     */
    public function addAction(TDataGridAction $action)
    {
        $this->actions[] = $action;
        $button = new GtkButton($action->getLabel());
        
        if (file_exists('lib/adianti/images/'.$action->getImage()))
        {
            $button->set_image(GtkImage::new_from_file('lib/adianti/images/'.$action->getImage()));
        }
        else
        {
            $button->set_image(GtkImage::new_from_file('app/images/'.$action->getImage()));
        }
        
        $button->connect_simple('clicked', array($this, 'onExecute'), $action);
        $this->action_area->pack_start($button);
    }
    
    /**
     * Execute the action
     * @param  $action A TDataGridAction object
     * @ignore-autocomplete on
     */
    public function onExecute(TDataGridAction $action)
    {
        $selection = $this->view->get_selection();
        if ($selection)
        {
            list($model, $iter) = $selection->get_selected();
            if ($iter)
            {
                $activeObject = $this->model->get_value($iter, $this->count);
                $field = $action->getField();
                $label = $action->getLabel();
                
                if (is_null($field))
                {
                    throw new Exception(TAdiantiCoreTranslator::translate('Field for action ^1 not defined', $label) . '.<br>' . 
                                        TAdiantiCoreTranslator::translate('Use the ^1 method', 'setField'.'()').'.');
                }
                
                $array['key'] = $activeObject->$field;
                $callb = $action->getAction();
                
                if (is_array($callb))
                {
                    if (is_object($callb[0]))
                    {
                        $object = $callb[0];
                        $window_visible = $object->is_visible();
                        
                        // execute action
                        call_user_func($callb, $array);
                        
                        if (method_exists($object, 'show'));
                        {
                            // if the window wasn't visible before
                            // the operation, shows it. 
                            // Forms: window wasn't visible, then show.
                            // SeekBtns: window was visible, do nothing.
                            //           the operation itself closes the window. 
                            if (!$window_visible)
                            {
                                if ($object->get_child())
                                {
                                    $object->show();
                                }
                            }
                        }
                    }
                    else
                    {
                        $class  = $callb[0];
                        $method = $callb[1];
                        TApplication::executeMethod($class, $method, $array);
                    }
                }
                else
                {
                    // execute action
                    call_user_func($callb, $array);
                }
            }
        }
    }
    
    /**
     * Creates the DataGrid Structure
     */
    public function createModel()
    {
        $this->types[] = GObject::TYPE_PHP_VALUE;
        $this->model->set_column_types($this->types);
        $this->view->set_model($this->model);
    }
    
    /**
     * Add an object to the DataGrid
     * @param $object An Active Record Object
     */
    public function addItem($object)
    {
        $indice = 0;
        $iter = $this->model->append();
        foreach ($this->columns as $column)
        {
            $name = $column->getName();
            $cell = $object->$name;
            
            $function = $column->getTransformer();
            if ($function)
            {
                $cell= call_user_func($function, $cell);
            }
            
            $this->model->set($iter, $indice, (string) $cell);
            $indice ++;
        }
        $this->model->set($iter, $indice, $object);
        $this->objects ++;
    }
    
    /**
     * Clear the DataGrid contents
     */
    public function clear()
    {
        $this->model->clear();
    }
    
    /**
     * Returns the DataGrid's width
     */
    public function getWidth()
    {
        return $this->width + count($this->columns)*10;
    }
    
    /**
     * Executed when the user double click at the row
     * @ignore-autocomplete on
     */
    public function onDoubleClick()
    {
        if (isset($this->actions[0]))
        {
            $this->onExecute($this->actions[0]);
        }
    }
    
    /**
     * Differentiate lines by color
     * @ignore-autocomplete on
     */
    public function formatZebra($column, $cell, $model, $iter)
    {
        $path = $model->get_path($iter);
        $row_num = $path[0];
        $row_color = ($row_num%2==1) ? '#eeeeee' : '#ffffff';
        $cell->set_property('cell-background', $row_color);
    }
    
    /**
     * Assign a PageNavigation object
     * @param $pageNavigation object
     */
    public function setPageNavigation($pageNavigation)
    {
        $this->pageNavigation = $pageNavigation;
    }
    
    /**
     * Return the assigned PageNavigation object
     * @return TPageNavigation
     */
    public function getPageNavigation()
    {
        return $this->pageNavigation;
    }
}
?>