<?php
Namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Form\TField;
use Adianti\Widget\Form\TButton;
use Adianti\Core\AdiantiCoreTranslator;

use Exception;
use Gtk;
use GtkFrame;
use GtkWidget;

/**
 * Wrapper class to deal with forms
 *
 * @version    2.0
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TForm extends GtkFrame
{
    protected $fields;
    protected $fname;
    static private $forms;
    
    /**
     * Class Constructor
     * @param $name Form Name
     */
    public function __construct($name = 'my_form')
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
     * Returns the form object by its name
     */
    public static function getFormByName($name)
    {
        if (isset(self::$forms[$name]))
        {
            return self::$forms[$name];
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
     * get form data in a static way
     * for internal use
     * @ignore-autocomplete on
     * @param $form_name Form Name
     */
    public function retrieveData($form_name)
    {
        if (isset(self::$forms[$form_name]))
        {
            $instance = self::$forms[$form_name];
            return $instance->getData();
        }
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
    public function addField(AdiantiWidgetInterface $field)
    {
        if ($field instanceof GtkWidget)
        {
            $name = $field->getName();
            if (isset($this->fields[$name]))
            {
                throw new Exception(AdiantiCoreTranslator::translate('You have already added a field called "^1" inside the form', $name));
            }
            $this->fields[$name] = $field;
            $field->setFormName($this->fname);
        }
    }
    
    /**
     * Remove a form field
     * @param $field Object
     */
    public function delField(AdiantiWidgetInterface $field)
    {
        if ($this->fields)
        {
            foreach($this->fields as $name => $object)
            {
                if ($field === $object)
                {
                    unset($this->fields[$name]);
                }
            }
        }
    }
    
    /**
     * Remove all form fields
     */
    public function delFields()
    {
        $this->fields = array();
    }
    
    /**
     * Define wich are the form fields
     * @param $fields An array containing a collection of TField objects
     */
    public function setFields($fields)
    {
        if (is_array($fields))
        {
            foreach ($fields as $field)
            {
                $this->addField($field);
            }
        }
        else
        {
            throw new Exception(AdiantiCoreTranslator::translate('Method ^1 must receive a paremeter of type ^2', __METHOD__, 'Array'));
        }
    }
    
    /**
     * Returns a form field by its name
     * @param $name A string containing the field's name
     */
    public function getField($name)
    {
        if (isset($this->fields[$name]))
        {
            return $this->fields[$name];
        }
    }
    
    /**
     * Returns an array with the form fields
     * @return Array of form fields
     */
    public function getFields()
    {
        return $this->fields;
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
                    $obj = $this->fields[$property];
                    if (method_exists($obj, 'onExecuteExitAction'))
                    {
                        call_user_func(array($obj, 'onExecuteExitAction'));
                    }
                }
            }
            else
            {
                $parts = explode('_', $property); // authors_list_name
                if (count($parts) == 3)
                {
                    $mtfproperty = $parts[0] . '_' . $parts[1];
                    
                    if (isset($this->fields[$mtfproperty])) // subfield of TMutifield in TSeekButton
                    {
                        $new_property = $parts[2];
                        $new_object = new StdClass;
                        $new_object->$new_property = $value;
                        $this->fields[$mtfproperty]->setFormData($new_object);
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
        $errors = array();
        foreach ($this->fields as $object)
        {
            if (!$object instanceof TButton)
            {
                if (method_exists($object, 'validate'))
                {
                    try
                    {
                        $object->validate();
                    }
                    catch (Exception $e)
                    {
                        $errors[] = $e->getMessage() . '.';
                    }
                }
            }
        }
        
        if (count($errors) > 0)
        {
            throw new Exception(implode("<br>", $errors));
        }
    }
    
    /**
     * Returns the child object
     */
    public function getChild()
    {
        return parent::get_child();
    }
    
    /**
     * Shows the form at the screen
     */
    public function show()
    {
        /* Não é possível, pois pode ter uma IF (Designer) somente com datagrid
        if (count($this->fields) == 0)
        {
            throw new Exception(AdiantiCoreTranslator::translate('Use the addField() or setFields() to define the form fields'));
        }
        */
        
        $child = parent::get_child();
        if ($child)
        {
            $child->show();
        }
        parent::show_all();
    }
}
