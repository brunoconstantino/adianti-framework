<?php
Namespace Adianti\Widget\Datagrid;

use Adianti\Core\AdiantiCoreApplication;
use Adianti\Core\AdiantiCoreTranslator;

use Exception;
use Gtk;
use GObject;
use GtkVBox;
use GtkTreeView;
use GtkListStore;
use GtkHButtonBox;
use GtkScrolledWindow;
use GtkButton;
use GtkImage;

/**
 * DataGrid Widget: Allows to create datagrids with rows, columns and actions
 *
 * @version    2.0
 * @package    widget
 * @subpackage datagrid
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TDataGrid extends GtkVBox
{
    private $count;
    private $columns;
    private $types;
    private $width;
    private $height;
    private $scrollable;
    private $action_area;
    private $pageNavigation;
    private $clickhandler;
    private $modelCreated;
    
    /**
     * Class Constructor
     */
    public function __construct()
    {
        $this->types   = array();
        $this->columns = array();
        $this->count   = 0;
        $this->modelCreated = FALSE;
        
        parent::__construct();
        $this->view  = new GtkTreeView;
        $this->model = new GtkListStore;
        $this->view->set_size_request(200,-1);
        $this->handler = $this->view->connect_simple('row-activated', array($this, 'onDoubleClick'));
        
        $this->action_area = new GtkHButtonBox;
        $this->action_area->set_layout(Gtk::BUTTONBOX_START);
        parent::pack_start($this->view, true, true);
        parent::pack_start($this->action_area, false, false);
        parent::set_size_request(-1,-1);
    }
    
    /**
     * Make the datagrid scrollable
     */
    public function makeScrollable()
    {
        $this->scrollable = TRUE;
        $children = parent::get_children();
        if ($children)
        {
            foreach ($children as $child)
            {
                parent::remove($child);
            }
        }
        $scroll = new GtkScrolledWindow;
        $scroll->add($this->view);
        $scroll->set_size_request(-1, $this->height);
        parent::pack_start($scroll, true, true);
        parent::pack_start($this->action_area, false, false);
        
        if ($this->height)
        {
            parent::set_size_request(-1,$this->height);
        }
    }
    
    /**
     * disable the default click action
     */
    public function disableDefaultClick()
    {
        $this->view->disconnect($this->handler);
    }
    
    /**
     * Define the Height
     * @param $height An integer containing the height
     */
    public function setHeight($height)
    {
        $this->height = $height;
        if ($this->scrollable)
        {
            parent::set_size_request(-1,$height);
        }
    }
    
    /**
     * Add a Column to the DataGrid
     * @param $object A TDataGridColumn object
     */
    public function addColumn(TDataGridColumn $column)
    {
        if ($this->modelCreated)
        {
            throw new Exception(AdiantiCoreTranslator::translate('You must call ^1 before ^2', __METHOD__ , 'createModel') );
        }
        else
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
    }
    
    /**
     * Add an Action to the DataGrid
     * @param $object A TDataGridAction object
     */
    public function addAction(TDataGridAction $action)
    {
        if ($this->modelCreated)
        {
            throw new Exception(AdiantiCoreTranslator::translate('You must call ^1 before ^2', __METHOD__ , 'createModel') );
        }
        else
        {
            $this->actions[] = $action;
            $button = new GtkButton($action->getLabel());
            
            if ($action->getImage())
            {
                if (file_exists('lib/adianti/images/'.$action->getImage()))
                {
                    $button->set_image(GtkImage::new_from_file('lib/adianti/images/'.$action->getImage()));
                }
                else if (file_exists('app/images/'.$action->getImage()))
                {
                    $button->set_image(GtkImage::new_from_file('app/images/'.$action->getImage()));
                }
            }
            
            $button->connect_simple('clicked', array($this, 'onExecute'), $action);
            $this->action_area->pack_start($button);
        }
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
                    throw new Exception(AdiantiCoreTranslator::translate('Field for action ^1 not defined', $label) . '.<br>' . 
                                        AdiantiCoreTranslator::translate('Use the ^1 method', 'setField'.'()').'.');
                }
                
                $array['key'] = $activeObject->$field;
                $callb = $action->getAction();
                
                if (is_array($callb))
                {
                    if (is_object($callb[0]))
                    {
                        $object = $callb[0];
                        $window_visible = method_exists($object, 'get_visible') ? $object->get_visible() : $object->is_visible();
                        
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
                        AdiantiCoreApplication::executeMethod($class, $method, $array);
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
     * Execute an edit action
     * @param $action    Callback to be executed
     * @param $parameter User parameters
     * @ignore-autocomplete on
     */
    function onExecuteEditAction($cell_renderer, $linha, $content, $column)
    {
        $parameters = $column->getEditAction()->getParameters();
        
        $selection = $this->view->get_selection();
        if ($selection)
        {
            list($model, $iter) = $selection->get_selected();
            if ($iter)
            {
                $activeObject = $this->model->get_value($iter, $this->count);
                $field = $column->getEditAction()->getField();
                
                $parameters['field'] = $column->getName();
                $parameters['key']   = $activeObject->{$field};
                $parameters['value'] = $content;
                
                call_user_func($column->getEditAction()->getAction(), $parameters);
            }
        }
    }
    
    /**
     * Creates the DataGrid Structure
     */
    public function createModel()
    {
        // the last iter is the object itself
        $this->types[] = GObject::TYPE_PHP_VALUE;
        $this->model->set_column_types($this->types);
        $this->view->set_model($this->model);
        
        foreach ($this->columns as $column)
        {
            if ($column->getEditAction())
            {
                $renderer = $column->getRenderer();
                $renderer->set_property('editable', TRUE);
                $renderer->connect("edited", array($this, 'onExecuteEditAction'), $column);
            }
        }
        $this->modelCreated = TRUE;
    }
    
    /**
     * Add an object to the DataGrid
     * @param $object An Active Record Object
     */
    public function addItem($object)
    {
        if ($this->modelCreated)
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
                    $cell = call_user_func($function, $cell, $object, $iter);
                }
                
                $this->model->set($iter, $indice, (string) $cell);
                $indice ++;
            }
            // the last iter is the object itself
            $this->model->set($iter, $indice, $object);
            $this->objects ++;
        }
        else
        {
            throw new Exception(AdiantiCoreTranslator::translate('You must call ^1 before ^2', 'createModel', __METHOD__ ) );
        }
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
