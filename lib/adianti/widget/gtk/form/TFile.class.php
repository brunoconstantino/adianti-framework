<?php
/**
 * FileChooser widget
 *
 * @version    1.0
 * @package    widget_gtk
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TFile extends GtkFileChooserButton
{
    private $wname;
    private $validations;
    
    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct($name)
    {
        $this->wname = $name;
        $this->validations = array();
        parent::__construct(TAdiantiCoreTranslator::translate('Open'), GTK::FILE_CHOOSER_ACTION_OPEN);
        parent::set_size_request(200, -1);
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
     * Define the widget's content
     * @param  $value  widget's content
     */
    public function setValue($value)
    {
        parent::set_filename($value);
    }

    /**
     * Return the widget's content
     */
    public function getValue()
    {
        return parent::get_filename();
    }
    
    /**
     * Define the widget's size
     * @param $size Widget's size in pixels
     */
    public function setSize($size)
    {
        $this->set_size_request($size,-1);
    }
    
    /**
     * Define if the widget is editable
     * @param $boolean A boolean
     */
    public function setEditable($editable)
    {
        parent::set_sensitive($editable);
    }
    
    /**
     * Define widget properties
     * @param  $property Property's name
     * @param  $value    Property's value
     */
    public function setProperty($property, $value)
    {
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