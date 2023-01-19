<?php
/**
 * Base class to construct all the widgets
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
abstract class TField
{
    protected $name;
    protected $size;
    protected $value;
    protected $editable;
    protected $tag;
    private   $validations;
    
    /**
     * Class Constructor
     * @param  $name name of the field
     */
    public function __construct($name)
    {
        // define some default properties
        self::setEditable(TRUE);
        self::setName($name);
        self::setSize(200);
        
        // initialize validations array
        $this->validations = array();
        
        TPage::include_css('lib/adianti/include/tfield/tfield.css');
        
        // creates a <input> tag
        $this->tag = new TElement('input');
        $this->tag->{'class'} = 'tfield';   // classe CSS
    }
    
    /**
     * Define the field's name
     * @param $name   A string containing the field's name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the field's name
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Define the field's value
     * @param $value A string containing the field's value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
    
    /**
     * Returns the field's value
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * Return the post data
     */
    public function getPostData()
    {
        if (isset($_POST[$this->name]))
        {
            return $_POST[$this->name];
        }
        else
        {
            return '';
        }
    }
    
    /**
     * Define if the field is editable
     * @param $editable A boolean
     */
    public function setEditable($editable)
    {
        $this->editable= $editable;
    }

    /**
     * Returns if the field is editable
     * @return A boolean
     */
    public function getEditable()
    {
        return $this->editable;
    }
    
    /**
     * Define a field property
     * @param $name  Property Name
     * @param $value Property Value
     */
    public function setProperty($name, $value)
    {
        // delegates the property assign to the composed object
        $this->tag->$name = $value;
    }
    
    /**
     * Define the Field's width
     * @param $width Field's width in pixels
     */
    public function setSize($width, $height = NULL)
    {
        $this->size = $width;
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
}
?>