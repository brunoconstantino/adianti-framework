<?php
/**
 * Wrapper class to deal with forms
 *
 * @version    1.0
 * @package    widget_gtk
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TForm extends GtkFrame
{
    protected $fields;
    protected $name;
    static private $forms;
    
    /**
     * Class Constructor
     * @param $name Form Name
     */
    public function __construct($name = NULL)
    {
        parent::__construct();
        parent::set_shadow_type(Gtk::SHADOW_NONE);
        // register this form
        self::$forms[$name] = $this;
        if ($name)
        {
            $this->setName($name);
        }
    }
    
    /**
     * Define the form name
     * @param $name A string containing the form name
     */
    public function setName($name)
    {
        $this->fname = $name;
    }
    
    /**
     * Returns the form name
     */
    public function getName()
    {
        return $this->fname;
    }
    
    
    /**
     * Send data for any form by it's name
     * @param $form_name Form Name
     * @param $object    An Object containing the form data
     */
    public function sendData($form_name, $object)
    {
        $instance = self::$forms[$form_name];
        $instance->setFilledData($object);
    }
    
    /**
     * Define if the form will be editable
     * @param $bool A Boolean indicating if the form will be editable
     */
    public function setEditable($bool)
    {
        foreach ($this->fields as $field)
        {
            $field->setEditable($bool);
        }
    }

    /**
     * Add a Form Field
     * @param $field Object
     */
    public function addField($field)
    {
        if ($field instanceof GtkWidget)
        {
            $name = $field->getName();
            $this->fields[$name] = $field;
            
            if ($field instanceof TButton)
            {
                $field->setFormName($this->name);
            }
        }
    }
    
    /**
     * Define wich are the form fields
     * @param $fields An array containing a collection of TField objects
     */
    public function setFields($fields)
    {
        foreach ($fields as $field)
        {
            $this->addField($field);
        }
    }
    
    /**
     * Returns a form field by its name
     * @param $name A string containing the field's name
     */
    public function getField($name)
    {
        return $this->fields[$name];
    }
    
    /**
     * Clear the form data
     */
    public function clear()
    {
        foreach ($this->fields as $name => $field)
        {
            if (!$field instanceof TButton)
            {
                $field->setValue(NULL);
            }
        }
    }
    
    /**
     * Define the data of the form
     * @param $object An Active Record object
     */
    public function setData($object)
    {
        foreach ($this->fields as $name => $field)
        {
            if (!$field instanceof TButton)
            {
                if (isset($object->$name))
                {
                    $field->setValue($object->$name);
                }
                else
                {
                    $field->setValue(NULL);
                }
            }
        }
    }
    
    /**
     * for internal use
     * @ignore-autocomplete on
     **/
    private function setFilledData($object)
    {
        $properties = get_object_vars($object);
        foreach ($properties as $property => $value)
        {
            if (isset($this->fields[$property]) AND is_object($this->fields[$property]) )
            {
                if (isset($object->$property) AND is_object($object->$property)) //TMultifield entire object
                {
                    $this->fields[$property]->setFormData($object->$property);
                }
                else // regular field
                {
                    $this->fields[$property]->setValue($object->$property);
                }
            }
            else
            {
                $parts = explode('_', $property, 2); // authors_author_name
                if (count($parts) == 2)
                {
                    if (isset($this->fields[$parts[0]])) // subfield of TMutifield in TSeekButton
                    {
                        $new_property = $parts[1];
                        $new_object=new StdClass;
                        $new_object->$new_property = $value;
                        $this->fields[$parts[0]]->setFormData($new_object);
                    }
                }
            }
        }
    }
    
    /**
     * Returns the form data as an object
     * @param $class A string containing the class to the returning object
     */
    public function getData($class = 'StdClass')
    {
        $object = new $class;
        if ($this->fields)
        {
            foreach ($this->fields as $field)
            {
                if (!$field instanceof TButton)
                {
                    $name = $field->getName();
                    $value= $field->getValue();
                    $object->$name = $value;
                }
            }
        }
        return $object;
    }
    
    /**
     * Validate form
     */
    public function validate()
    {
        foreach ($this->fields as $object)
        {
            if (!$object instanceof TButton)
            {
                if (method_exists($object, 'validate'))
                {
                    $object->validate();
                }
            }
        }
    }
    
    /**
     * Shows the form at the screen
     */
    public function show()
    {
        $child = parent::get_child();
        if ($child)
        {
            $child->show();
        }
        parent::show_all();
    }
}
?>