<?php
Namespace Adianti\Widget\Form;

use Adianti\Validator\TFieldValidator;

use Gtk;
use GtkHBox;

/**
 * Base class to construct all the widgets
 *
 * @version    2.0
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
abstract class TField extends GtkHBox
{
    protected $widget;
    protected $wname;
    protected $formName;
    private   $validations;
    protected $wid;
    
    /**
     * Class Constructor
     * @param  $name name of the field
     */
    public function __construct($name)
    {
        parent::__construct();
        self::setName($name);
        $this->wid = uniqid();

        // initialize validations array
        $this->validations = array();
    }
    
    /**
     * Define the field's name
     * @param $name   A string containing the field's name
     */
    public function setName($name)
    {
        $this->wname = $name;
    }

    /**
     * Returns the field's name
     */
    public function getName()
    {
        return $this->wname;
    }
    
    /**
     * Define the field's id
     * @param $id   A string containing the field's id
     */
    public function setId($id)
    {
        $this->wid = $id;
    }

    /**
     * Returns the field's id
     */
    public function getId()
    {
        return $this->wid;
    }
    
    /**
     * Define a field property
     * @param $name  Property Name
     * @param $value Property Value
     */
    abstract public function setProperty($name, $value, $replace = TRUE);
    
    /**
     * Return a field property
     * @param $name  Property Name
     * @param $value Property Value
     */
    abstract public function getProperty($name);
        
    /**
     * Define the Field's size
     * @param $width Field's width in pixels
     */
    abstract public function setSize($width, $height = NULL);
    
    /**
     * Define the name of the form to wich the button is attached
     * @param $name    A string containing the name of the form
     * @ignore-autocomplete on
     */
    public function setFormName($name)
    {
        $this->formName = $name;
    }
    
    /**
     * Define the field's tooltip
     * @param $tip A string containing the field's tooltip
     */
    public function setTip($tip)
    {
        if (method_exists($this, 'set_tooltip_text'))
        {
            $this->widget->set_tooltip_text($tip);
        }
        else
        {
            $tooltip = TooltipSingleton::getInstance();
            $tooltip->set_tip($this->widget, $tip);
        }
    }
    
    /**
     * Define if the field is editable
     * @param $editable A boolean
     */
    public function setEditable($editable)
    {
        $this->widget->set_sensitive($editable);
    }

    /**
     * Returns if the field is editable
     * @return A boolean
     */
    public function getEditable()
    {
        return $this->widget->get_sensitive();
    }
    
    /**
     * Add a field validator
     * @param $label Field name
     * @param $validator TFieldValidator object
     * @param $parameters Aditional parameters
     */
    public function addValidation($label, TFieldValidator $validator, $parameters = NULL)
    {
        $this->validations[] = array($label, $validator, $parameters);
    }
    
    /**
     * Validate a field
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
    
    /**
     * Enable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function enableField($form_name, $field)
    {
        $form = TForm::getFormByName($form_name);
        if ($form instanceof TForm)
        {
            $field = $form->getField($field);
            if ($field instanceof AdiantiWidgetInterface)
            {
                $field->setEditable(TRUE);
            }
        }
    }
    
    /**
     * Disable the field
     * @param $form_name Form name
     * @param $field Field name
     */
    public static function disableField($form_name, $field)
    {
        $form = TForm::getFormByName($form_name);
        if ($form instanceof TForm)
        {
            $field = $form->getField($field);
            if ($field instanceof AdiantiWidgetInterface)
            {
                $field->setEditable(FALSE);
            }
        }
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
                $field->setValue(NULL);
            }
        }
    }
    
    /**
     * Connects a signal to the widget
     */
    public function connect_simple($signal, $callback, $parameters)
    {
        $this->widget->connect_simple($signal, $callback, $parameters);
    }
    
    /**
     * Connects a signal to the widget
     */
    public function connect($signal, $callback, $parameters)
    {
        $this->widget->connect($signal, $callback, $parameters);
    }
    
    /**
     * Call a non existant method: Redirect to composed widget
     * @param $method Method name
     * @param $parameters Array of parameters
     */
    public function __call($method, $parameters)
    {
        call_user_func_array(array($this->widget, $method), $parameters);
    }
    
    /**
     * Show widget
     */
    public function show()
    {
        $this->widget->show_all();
    }
}
