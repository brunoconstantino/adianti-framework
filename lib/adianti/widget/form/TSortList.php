<?php
Namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Form\TField;
use Adianti\Widget\Form\TImage;

use Gtk;
use Gdk;
use GtkFrame;
use GtkHBox;
use GtkVBox;
use GtkScrolledWindow;
use GtkButton;

/**
 * A Sortable list
 *
 * @version    2.0
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TSortList extends TField implements AdiantiWidgetInterface
{
    private $container;
    private $itemIcon;
    private $items;
    private $connectedTo;
    private $initialItems;
    protected $widget;
    
    /**
     * Class Constructor
     * @param  $name widget's name
     */
    function __construct($name)
    {
        parent::__construct($name);
        
        $this->widget = new GtkFrame;
        parent::add($this->widget);
        
        $this->container = new GtkVBox;
        
        $this->initialItems = array();
        $this->items = array();
        
        $targets = array(array('text/plain', 0, -1));
        $this->widget->connect('drag_data_received', array($this, 'onDataReceived'));
        $this->widget->drag_dest_set(Gtk::DEST_DEFAULT_ALL, $targets, Gdk::ACTION_COPY);
        
        $scroll = new GtkScrolledWindow;
        $scroll->set_border_width(4);
        $this->widget->add($scroll);
        
        $hbox = new GtkHBox;
        $scroll->add_with_viewport($hbox);
        
        $hbox->pack_start($this->container, TRUE, TRUE);
        $hbox->set_border_width(20);
        parent::show_all();
    }
    
    /**
     * Define the widget's content
     * @param  $value  widget's content
     */
    public function setValue($value)
    {
        foreach ($this->container->get_children() as $child)
        {
            $this->container->remove($child);
        }
        $items = $this->initialItems;
        
        if (is_array($value))
        {
            $this->items = array();
            foreach ($value as $index)
            {
                if (isset($items[$index]))
                {
                    $this->addItems(array($index => $items[$index]));
                }
                else if (isset($this->connectedTo) AND is_array($this->connectedTo))
                {
                    foreach ($this->connectedTo as $connectedList)
                    {
                        if (isset($connectedList->initialItems[$index] ) )
                        {
                            $this->addItems(array($index => $connectedList->initialItems[$index]));
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Return the widget's content
     */
    public function getValue()
    {
        $return = array();
        foreach ($this->container->get_children() as $child)
        {
            $return[] = $child->get_data('key');
        }
        return $return;
    }
    
    /**
     * Define the Field's size
     * @param $width Field's width in pixels
     */
    public function setSize($width, $height = NULL)
    {
        $this->widget->set_size_request($width +40, $height + 40);
    }
    
    /**
     * Not implemented
     */
    public function setProperty($name, $value, $replace = TRUE)
    {}
    
    /**
     * Not implemented
     */
    public function getProperty($name)
    {}
    
    /**
     * Define the item icon
     * @param $image Item icon
     */
    public function setItemIcon(TImage $icon)
    {
        $this->itemIcon = $icon;
    }
    
    /**
     * Connect to another list
     * @param $list Another TSortList
     */
    public function connectTo(TSortList $list)
    {
        $this->connectedTo[] = $list;
    }
    
    /**
     * Add items to the sort list
     * @param $items An indexed array containing the options
     */
    public function addItems($items)
    {
        if (is_array($items))
        {
            $this->items += $items;
            $this->initialItems += $items;
            
            foreach ($items as $key=>$value)
            {
                $this->addItem($key, $value);
            }
        }
    }
    
    /**
     * Return the sort items
     */
    public function getItems()
    {
        return $this->initialItems;
    }
    
    /**
     * Returns the widget Selection Data for Drag action
     */
    public function onDragDataGet($widget, $context, $selection)
    {
        $key   = $widget->get_data('key');
        $value = $widget->get_data('value');
        $selection->set_text(base64_encode(serialize(array($key => $value))));
    }
    
    /**
     * Executes the action for the Drop action
     */
    function onDataReceived($widget, $context, $x, $y, $selection)
    {
        // obtém os dados da seleção
        $data = unserialize(base64_decode($selection->get_text()));
        
        $this->items += $data;
        $child = $this->addItem(key($data), current($data));
    }
    
    /**
     * Add an item to the Sort list
     * @param $key Item key
     * @param $value Item value
     */
    private function addItem($key, $value)
    {
        $button = new GtkButton($value);
        
        $button->set_data('key',   $key);
        $button->set_data('value', $value);
        //$hbox = new GtkHBox;
        //$hbox->pack_start($button, TRUE, TRUE);
        $this->container->pack_start($button, FALSE, FALSE);
        $button->show();
        $targets = array(array('text/plain', 0, -1));
        $button->connect('drag_data_get', array($this, 'onDragDataGet'));
        $button->connect_simple('drag_data_get', array($this->container, 'remove'), $button);
        
        $button->drag_source_set(Gdk::BUTTON1_MASK | Gdk::BUTTON3_MASK | Gdk::SHIFT_MASK, $targets, Gdk::ACTION_COPY);
        $button->drag_source_set_icon_stock(Gtk::STOCK_INDEX);
        
        return $button;
    }
    
    /**
     * Clear the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function clearField($form_name, $field)
    {
        $form = TForm::getFormByName($form_name);
        if ($form instanceof TForm)
        {
            $field = $form->getField($field);
            if ($field instanceof AdiantiWidgetInterface)
            {
                $field->setValue(array());
            }
        }
    }
}
