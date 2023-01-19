<?php
/**
 * Wrapper class to deal with forms
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TForm
{
    protected $fields; // array containing the form fields
    private   $name;   // form name
    private   $js_function;
    
    /**
     * Class Constructor
     * @param $name Form Name
     */
    public function __construct($name = 'my_form')
    {
        $this->setName($name);
    }
    
    /**
     * Define the form name
     * @param $name A string containing the form name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * Returns the form name
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Send data for a form located in the parent window
     * @param $form_name  Form Name
     * @param $object     An Object containing the form data
     */
    public static function sendData($form_name, $object, $aggregate = FALSE)
    {
        // iterate the object properties
        if ($object)
        {
            foreach ($object as $field => $value)
            {
                if (is_object($value))  // TMultiField
                {
                    foreach ($value as $property=>$data)
                    {
                        // if inside ajax request, then utf8_encode if isn't utf8
                        if (utf8_encode(utf8_decode($data)) !== $data )
                        {
                            $data = utf8_encode(addslashes($data));
                        }
                        else
                        {
                            $data = addslashes($data);
                        }
                        // send the property value to the form
                        $script = new TElement('script');
                        $script->{'language'} = 'JavaScript';
                        $script->setUseSingleQuotes(TRUE);
                        $script->setUseLineBreaks(FALSE);
                        $script->add( "document.{$form_name}.{$field}_{$property}.value = '{$data}';" );
                        $script->show();
                        //echo "window.opener.document.{$form_name}.{$field}_{$property}.value = '{$data}';";
                    }
                }
                else
                {
                    // if inside ajax request, then utf8_encode if isn't utf8
                    if (utf8_encode(utf8_decode($value)) !== $value )
                    {
                        $value = utf8_encode(addslashes($value));
                    }
                    else
                    {
                        $value = addslashes($value);
                    }
                    // send the property value to the form
                    $script = new TElement('script');
                    $script->{'language'} = 'JavaScript';
                    $script->setUseSingleQuotes(TRUE);
                    $script->setUseLineBreaks(FALSE);
                    if ($aggregate)
                    {
                        $script->add( "if (document.{$form_name}.{$field}.value == \"\") { document.{$form_name}.{$field}.value  = '{$value}'; } else { document.{$form_name}.{$field}.value = document.{$form_name}.{$field}.value + ', {$value}' }" );
                    }
                    else
                    {
                        $script->add( "document.{$form_name}.{$field}.value = '{$value}';" );
                    }
                    $script->show();
                    //echo "window.opener.document.{$form_name}.{$field}.value = '{$value}';";
                }
            }
        }
    }
    
    /**
     * Define if the form will be editable
     * @param $bool A Boolean
     */
    public function setEditable($bool)
    {
        if ($this->fields)
        {
            foreach ($this->fields as $object)
            {
                $object->setEditable($bool);
            }
        }
    }
    
    /**
     * Add a Form Field
     * @param $field Object
     */
    public function addField($field)
    {
        if ($field instanceof TField)
        {
            $name = $field->getName();
            if ($name)
            {
                $this->fields[$name] = $field;
                
                if ($field instanceof TButton)
                {
                    $field->setFormName($this->name);
                    $field->addFunction($this->js_function);
                }
                if ($field instanceof TSeekButton OR $field instanceof TMultiField)
                {
                    $field->setFormName($this->name);
                }
            }
        }
        if ($field instanceof TMultiField)
        {
            $this->js_function .= "mtf{$name}.parseTableToJSON();";
            
            if ($this->fields)
            {
                // if the button was added before multifield
                foreach ($this->fields as $field)
                {
                    if ($field instanceof TButton)
                    {
                        $field->addFunction($this->js_function);
                    }
                }
            }
        }
    }
    
    /**
     * Define wich are the form fields
     * @param $fields An array containing a collection of TField objects
     */
    public function setFields($fields)
    {
        $this->js_function = '';
        // iterate the form fields
        foreach ($fields as $field)
        {
            $this->addField($field);
        }
    }
    
    /**
     * Returns a form field by its name
     * @param $name  A string containing the field's name
     * @return       The Field object
     */
    public function getField($name)
    {
        if (isset($this->fields[$name]))
        {
            return $this->fields[$name];
        }
    }
    
    /**
     * clear the form Data
     */
    public function clear()
    {
        // iterate the form fields
        foreach ($this->fields as $name => $field)
        {
            if ($name) // labels don't have name
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
        // iterate the form fields
        foreach ($this->fields as $name => $field)
        {
            if ($name) // labels don't have name
            {
                if (isset($object->$name))
                {
                    $field->setValue($object->$name);
                }
            }
        }
    }
    
    /**
     * Returns the form data as an object
     * @param $class A string containing the class for the returning object
     */
    public function getData($class = 'StdClass')
    {
        $object = new $class;
        foreach ($this->fields as $key => $fieldObject)
        {
            if (!$fieldObject instanceof TButton)
            {
                $object->$key = $fieldObject->getPostData();
            }
        }
        
        return $object;
    }

    /**
     * Validate form
     */
    public function validate()
    {
        // assign post data before validation
        // validation exception would prevent
        // the user code to execute setData()
        $this->setData($this->getData());
        
        foreach ($this->fields as $fieldObject)
        {
            $fieldObject->validate();
        }
    }
    
    /**
     * Add a container to the form (usually a table of panel)
     * @param $object Any Object that implements the show() method
     */
    public function add($object)
    {
        $this->child = $object;
    }
    
    /**
     * Shows the form at the screen
     */
    public function show()
    {
        TPage::include_css('lib/adianti/include/tform/tform.css');
        
        // creates the form tag
        $tag = new TElement('form');
        $tag-> enctype="multipart/form-data";
        $tag-> name   = $this->name; // form name
        $tag-> id     = $this->name; // form id
        $tag-> method = 'post';      // transfer method
        
        // add the container to the form
        if (isset($this->child))
        {
            $tag->add($this->child);
        }
        // show the form
        $tag->show();
    }
}
?>