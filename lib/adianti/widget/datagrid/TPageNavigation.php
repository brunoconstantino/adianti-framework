<?php
Namespace Adianti\Widget\Datagrid;

use Adianti\Control\TAction;

use Exception;
use Gtk;
use GtkFrame;
use GtkHBox;
use GtkEventBox;
use GtkButton;
use GdkColor;

/**
 * Page Navigation provides navigation for a datagrid
 *
 * @version    2.0
 * @package    widget
 * @subpackage datagrid
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TPageNavigation extends GtkFrame
{
    private $limit;
    private $count;
    private $order;
    private $page;
    private $first_page;
    private $action;
    private $hbox;
    private $width;
    private $buttons;
    private $front;
    private $back;
    
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->hbox = new GtkHBox;
        $this->buttons = array();
        $box = new GtkEventBox;
        $a = new GdkColor(50000,50000,50000);
        $box->modify_bg(Gtk::STATE_NORMAL, $a);
        $box->add($this->hbox);
        
        $this->back = new GtkButton('<<');
        $this->back->set_relief(GTK::RELIEF_NONE);
        $this->back->connect('clicked', array($this, 'executeAction'));
        $this->back->set_sensitive(FALSE);
        
        $this->front = new GtkButton('>>');
        $this->front->set_relief(GTK::RELIEF_NONE);
        $this->front->connect('clicked', array($this, 'executeAction'));
        $this->front->set_sensitive(FALSE);
        
        $this->hbox->pack_start(new GtkHBox, true, true);
        $this->hbox->pack_start($this->back, false, false);
        for ($n=1; $n<=10; $n++)
        {
            $this->buttons[$n] = new GtkButton($n);
            $this->buttons[$n]->get_child()->set_use_markup(TRUE);
            $this->buttons[$n]->set_relief(GTK::RELIEF_NONE);
            $this->buttons[$n]->connect('clicked', array($this, 'executeAction'));
            $this->buttons[$n]->set_sensitive(FALSE);
            $this->hbox->pack_start($this->buttons[$n], false, false);
        }
        $this->hbox->pack_start($this->front, false, false);
        $this->hbox->pack_start(new GtkHBox, true, true);
        parent::add($box);
    }
    
    /**
     * Set the Amount of displayed records
     * @param $limit An integer representing the Amount of displayed records
     */
    public function setLimit($limit)
    {
        $this->limit  = $limit;
        $this->refresh();
    }
    
    /**
     * Define the PageNavigation's width
     * @param $width PageNavigation's width
     */
    public function setWidth($width)
    {
        $this->width = $width;
        parent::set_size_request($this->width,-1);
    }
    
    /**
     * Define the total count of records
     * @param $count An integer (the total count of records)
     */
    public function setCount($count)
    {
        $this->count = $count;
        $this->refresh();
    }
    
    /**
     * Define the current page
     * @param $page An integer (the current page)
     */
    public function setPage($page)
    {
        $this->page = $page;
        $this->refresh();
    }
    
    /**
     * Define the first page
     * @param $first_page An integer (the current page)
     */
    public function setFirstPage($first_page)
    {
        $this->first_page = $first_page;
        $this->refresh();
    }
    
    /**
     * Define the ordering
     * @param $order A string containint the column name
     */
    public function setOrder($order)
    {
        $this->order = $order;
        $this->refresh();
    }
    
    /**
     * Set the page navigation properties
     * @param $properties array of properties
     */
    public function setProperties($properties)
    {
        $order      = isset($properties['order'])  ? addslashes($properties['order'])  : '';
        $page       = isset($properties['page'])   ? $properties['page']   : 1;
        $first_page = isset($properties['first_page']) ? $properties['first_page']: 1;
        
        $this->setOrder($order);
        $this->setPage($page);
        $this->setFirstPage($first_page);
    }
    
    /**
     * Define the PageNavigation action
     * @param $action TAction object (fired when the user navigates)
     */
    public function setAction($action)
    {
        $this->action = $action;
        $this->refresh();
    }
    
    /**
     * Refresh the Page Navigation's content
     */
    public function refresh()
    {
        $first_page  = $this->first_page;
        $direction   = 'asc';
        
        $page_size = $this->limit;
        $max = 10;
        $registros=$this->count;
        if (!$registros)
        {
            return;
        }
        if ($page_size > 0)
        {
            $pages = (int) ($registros / $page_size) - $first_page +1;
        }
        else
        {
            $pages = 1;
        }
        if ($page_size>0)
        {
            $resto = $registros % $page_size;
        }
        else
        {
            $resto = 0;
        }
        $pages += $resto>0 ? 1 : 0;
        $last_page = min($pages, $max);
        
        $this->back->set_sensitive(FALSE);
        $this->front->set_sensitive(FALSE);
        
        for ($n=1; $n<=10; $n++)
        {
            $this->buttons[$n]->set_sensitive(FALSE);
        }
        
        if ($first_page > 1)
        {
            $_first_page = $first_page - $max;
            $n = $_first_page;
            $offset = ($n -1) * $page_size;
            
            $param['offset']    = $offset;
            $param['limit']     = $this->limit;
            $param['page_size'] = $page_size;
            $param['page']      = $n;
            $param['first_page']= $_first_page;
            $param['order']     = $this->order;
            $this->back->set_sensitive(TRUE);
            $this->back->set_data('param', $param);
        }
        
        $i=1;
        for ($n=$first_page; $n <= $last_page + $first_page -1; $n++)
        {
            $offset = ($n -1) * $page_size;
            $label   = ($this->page == $n) ? "<b>$n</b>" : "$n";
            
            $param['offset']    = $offset;
            $param['limit']     = $this->limit;
            $param['page_size'] = $page_size;
            $param['page']      = $n;
            $param['first_page']= $first_page;
            $param['order']     = $this->order;
            $this->buttons[$i]->set_data('param', $param);
            $this->buttons[$i]->get_child()->set_markup($label);
            $this->buttons[$i]->set_sensitive(TRUE);
            $i++;
        }
        if ($pages > $max)
        {
            $offset = ($n -1) * $page_size;
            $first_page = $n;
            
            $param['offset']    = $offset;
            $param['limit']     = $this->limit;
            $param['page_size'] = $page_size;
            $param['page']      = $n;
            $param['first_page']= $first_page;
            $param['order']     = $this->order;
            $this->front->set_sensitive(TRUE);
            $this->front->set_data('param', $param);
        }
    }
    
    /**
     * execute the widget's action
     * @ignore-autocomplete on
     */
    public function executeAction($widget)
    {
        call_user_func($this->action->getAction(), $widget->get_data('param'));
        $this->refresh();
    }
    
    /**
     * Show the PageNavigation widget
     */
    public function show()
    {
        parent::show();
        $this->refresh();
    }
}
