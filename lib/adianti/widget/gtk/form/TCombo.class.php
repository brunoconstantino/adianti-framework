<?php
/**
 * ComboBox Widget
 *
 * @version    1.0
 * @package    widget_gtk
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TCombo extends GtkHBox
{
    private $widget;
    private $model;
    private $iters;
    private $validations;
    
    /**
     * Class Constructor
     * @param  $name widget's name
     */
    public function __construct($name)
    {
        parent::__construct();
        $this->widget = GtkComboBox::new_text();
        
        // create the combo model
        $this->model = new GtkListStore(GObject::TYPE_STRING, GObject::TYPE_STRING);
        $this->widget->set_model($this->model);
        
        $this->widget->set_size_request(200, -1);
        $this->wname = $name;
        parent::add($this->widget);
        $this->validations = array();
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
            $this->widget->set_active_iter($this->iters[$value]);
        }
        else
        {
            $this->widget->set_active(0);
        }
    }
    
    /**
     * Return the current item showed
     */
    public function getValue()
    {
        $iter  = $this->widget->get_active_iter();
        if ($iter)
        {
            $model = $this->widget->get_model();
            
            $valor = $model->get_value($iter, 1);
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
        $this->widget->set_size_request($size, -1);
    }
    
    /**
     * Define a callback for change
     * @param $callback PHP valid callback
     * @ignore-autocomplete on
     */
    public function setCallback($callback)
    {
        $this->widget->connect('changed', $callback);
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