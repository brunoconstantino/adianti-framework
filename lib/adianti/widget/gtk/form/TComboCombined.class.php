<?php
/**
 * ComboBox Widget with an entry
 *
 * @version    1.0
 * @package    widget_gtk
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TComboCombined extends GtkHbox
{
    private $combo;
    private $entry;
    private $model;
    private $iters;
    
    /**
     * Class Constructor
     * @param  $name widget's name
     * @param  $text widget's name
     */
    public function __construct($name, $text_name)
    {
        // executes the parent class constructor
        parent::__construct();
        
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
        $this->wname = $name;
        
        parent::pack_start($this->entry);
        parent::pack_start($this->combo);
        
        $this->validations = array();
    }
    
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
     * Add items to the combo box
     * @param $items An indexed array containing the options
     */
    public function addItems($items)
    {
        if ($items)
        {
            $this->model->append(array('', ''));
            foreach ($items as $key=>$value)
            {
                $this->iters[$key] = $this->model->append(array($value, $key));
            }
        }
    }
    
    /**
     * Define the widget's name 
     * @param $name Widget's Name
     */
    public function setName($name)
    {
        $this->wname = $name;
    }
    
    /**
     * Returns the name of the widget
     * @return A string containing the name of the widget
     */
    public function getName()
    {
        return $this->wname;
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
    
    // for compability reasons
    public function setProperty($property, $value) {}
    
    /**
     * Define the widget's size
     * @param $size Widget's size in pixels
     */
    public function setSize($size)
    {
        $this->combo->set_size_request($size,-1);
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
     * Add a field validator
     * @param $validator TFieldValidator object
     */
    public function addValidation($label, TFieldValidator $validator, $parameters = NULL)
    {
        $this->validations[] = array($label, $validator, $parameters);
    }
    
    /**
     * Validate a field
     * @param $validator TFieldValidator object
     */
    public function validate()
    {
        if ($this->validations)
        {
            foreach ($this->validations as $validation)
            {
                $label      = $validation[0];
                $validator  = $validation[1];
                $parameters = $validation[2];
                
                $validator->validate($label, $this->getValue(), $parameters);
            }
        }
    }
}
?>