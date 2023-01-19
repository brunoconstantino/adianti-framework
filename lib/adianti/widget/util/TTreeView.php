<?php
Namespace Adianti\Widget\Util;

use Gtk;
use GObject;
use GtkTreeView;
use GtkTreeStore;
use GtkTreeViewColumn;
use GtkCellRendererPixbuf;
use GtkCellRendererText;
use GdkPixbuf;

/**
 * TreeView
 * 
 * @version    2.0
 * @package    widget
 * @subpackage util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TTreeView extends GtkTreeView
{
    private $model;
    private $itemAction;
    private $itemIcon;
    private $paths;
    
    /**
     * Construct treeview
     */
    function __construct()
    {
        parent::__construct();
        parent::set_headers_visible(FALSE);
        $this->model = new GtkTreeStore(GObject::TYPE_OBJECT, GObject::TYPE_STRING, GObject::TYPE_PHP_VALUE, GObject::TYPE_STRING);
        parent::set_model($this->model);
        parent::connect('row-activated', array($this, 'onClick'));
        $column1 = new GtkTreeViewColumn;
        $cell_renderer1 = new GtkCellRendererPixbuf;
        $cell_renderer2 = new GtkCellRendererText;
        $column1->pack_start($cell_renderer1, false);
        $column1->pack_start($cell_renderer2, false);
        $column1->set_attributes($cell_renderer1, 'pixbuf', 0);
        $column1->set_attributes($cell_renderer2, 'text', 1);
        parent::append_column($column1);
    }
    
    /**
     * Set size
     * @param $size width
     */
    public function setSize($width)
    {
        parent::set_size_request($width, -1);
    }
    
    /**
     * Set item icon
     * @param $icon icon location
     */
    public function setItemIcon($icon)
    {
        $this->itemIcon = $icon;
    }
    
    /**
     * Set item action
     * @param $action icon action
     */
    public function setItemAction($action)
    {
        $this->itemAction = $action;
    } 
    
    /**
     * Collapse the Tree
     */
    public function collapse()
    {
        parent::collapse_all();
    }
    
    /**
     * Fill treeview from an multi-dimensional array
     * @param multi-dimensional array
     */
    public function fromArray($array)
    {
        if (is_array($array))
        {
            foreach ($array as $key => $option)
            {
                if (is_scalar($option))
                {
                    $iter = $this->model->append();
                    $pixbuf = GdkPixbuf::new_from_file('app/images/'.$this->itemIcon);
                    $this->model->set($iter, 0, $pixbuf);
                    $this->model->set($iter, 1, $option);
                    $this->model->set($iter, 2, array('key'=> $key, 'value'=>$option));
                    $this->model->set($iter, 3, 'child');
                    
                    $path = $this->model->get_path($iter);
                    $this->paths[$key] = $path;
                }
                else if (is_array($option))
                {
                    $iter = $this->model->append();
                    $pixbuf = GdkPixbuf::new_from_file('lib/adianti/include/ttreeview/ico_folder.png');
                    $this->model->set($iter, 0, $pixbuf);
                    $this->model->set($iter, 1, $key);
                    $this->model->set($iter, 2, array('key'=> $key, 'value'=>$option));
                    $this->model->set($iter, 3, 'parent');
                    
                    $this->fromOptions($iter, $option);
                }
            }
        }
        parent::expand_all();
    }
    
    /**
     * Expand to Tree Node
     * @param $key Node key
     */
    public function expandTo($key)
    {
        parent::expand_to_path( $this->paths[$key] );
    }
    
    /**
     * Fill one level of the treeview
     * @param $options array of options
     * @ignore-autocomplete on
     */
    private function fromOptions($parent, $options)
    {
        if (is_array($options))
        {
            foreach ($options as $key => $option)
            {
                if (is_scalar($option))
                {
                    $iter = $this->model->append($parent);
                    $pixbuf = GdkPixbuf::new_from_file('app/images/'.$this->itemIcon);
                    $this->model->set($iter, 0, $pixbuf);
                    $this->model->set($iter, 1, $option);
                    $this->model->set($iter, 2, array('key'=> $key, 'value'=>$option));
                    $this->model->set($iter, 3, 'child');
                    
                    $path = $this->model->get_path($iter);
                    $this->paths[$key] = $path;
                }
                else if (is_array($option))
                {
                    $iter = $this->model->append($parent);
                    $pixbuf = GdkPixbuf::new_from_file('lib/adianti/include/ttreeview/ico_folder.png');
                    $this->model->set($iter, 0, $pixbuf);
                    $this->model->set($iter, 1, $key);
                    $this->model->set($iter, 2, array('key'=> $key, 'value'=>$option));
                    $this->model->set($iter, 3, 'parent');
                    
                    $this->fromOptions($iter, $option);
                }
            }
        }
    }
    
    /**
     * Execute the action
     * @ignore-autocomplete on
     */
    public function onClick($object, $event)
    {
        $treeselection = parent::get_selection();
        list($model, $iter) = $treeselection->get_selected();
        
        if ($iter)
        {
            $info = $this->model->get_value($iter, 2);
            $type = $this->model->get_value($iter, 3);
            
            if ($type == 'child' AND $this->itemAction)
            {
                $parameters = $this->itemAction->getParameters();
                $parameters['key'] = $info['key'];
                $parameters['value'] = $info['value'];
                call_user_func_array($this->itemAction->getAction(), array($parameters));
            }
        }
    }
}
