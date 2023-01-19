<?php
Namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Form\TField;

use Gtk;
use GObject;
use GtkHBox;
use GtkListStore;
use GtkEntry;
use GtkComboBox;

/**
 * ComboBox Widget with an entry
 *
 * @version    2.0
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TComboCombined extends TField implements AdiantiWidgetInterface
{
    private $combo;
    private $entry;
    private $model;
    private $iters;
    protected $widget;
    
    /**
     * Class Constructor
     * @param  $name widget's name
     * @param  $text widget's name
     */
    public function __construct($name, $text_name)
    {
        parent::__construct($name);
        
        $this->widget = new GtkHBox;
        parent::add($this->widget);
        
        $this->text_name = $text_name;
        
        // create the combo model
        $this->model = new GtkListStore(GObject::TYPE_STRING, GObject::TYPE_STRING);
        
        $this->entry = new GtkEntry;
        $this->entry->set_size_request(50, 25);
        $this->entry->set_sensitive(FALSE);
        
        $this->combo = GtkComboBox::new_text();
        $this->combo->set_model($this->model);    
        
        $this->combo->set_size_request(200, -1);
        $this->combo->connect_simple('changed', array($this, 'onComboChange'));
        
        $this->widget->pack_start($this->entry);
        $this->widget->pack_start($this->combo);
    }
    
    /**
     * Define wich item will be shown
     * @param $value  The item index
     */
    public function setValue($value)
    {
        if (isset($this->iters[$value]))
        {
            $this->combo->set_active_iter($this->iters[$value]);
            $this->entry->set_text($value);
        }
        else if ($value == '')
        {
            $this->combo->set_active(0);
            $this->entry->set_text('');
        }
    }
    
    /**
     * Return the current item showed
     */
    public function getValue()
    {
        $iter  = $this->combo->get_active_iter();
        if ($iter)
        {
            $model = $this->combo->get_model();
            $valor = $model->get_value($iter, 1);
            return $valor;
        }
    }
    
    /**
     * Define the Field's width
     * @param $width Field's width in pixels
     */
    public function setSize($width, $height = NULL)
    {
        $this->combo->set_size_request($width, -1);
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
     * Returns the text widget's name
     */
    public function getTextName()
    {
        return $this->text_name;
    }
    
    /**
     * Define the text widget's name
     * @param $name A string containing the text widget's name
     */
    public function setTextName($name)
    {
        $this->text_name = $name;
    }
    
    /**
     * Return the current item showed
     */
    public function getTextValue()
    {
        $iter  = $this->combo->get_active_iter();
        if ($iter)
        {
            $model = $this->combo->get_model();
            $valor = $model->get_value($iter, 0);
            return $valor;
        }
    }
    
    /**
     * Add items to the combo box
     * @param $items An indexed array containing the options
     */
    public function addItems($items)
    {
        if (is_array($items))
        {
            $this->model->append(array('', ''));
            foreach ($items as $key=>$value)
            {
                $this->iters[$key] = $this->model->append(array($value, $key));
            }
        }
    }
    
    /**
     * Fired when the user changes the combo option
     * Change the entry text according to the combo change
     * @ignore-autocomplete on
     */
    public function onComboChange()
    {
        $this->entry->set_text($this->getValue());
    }
    
    /**
     * Define editable
     * @param $editable boolean
     */
    public function setEditable($editable)
    {
        $this->entry->set_sensitive($editable);
        $this->combo->set_sensitive($editable);
    }
}
