<?php
/**
 * A group of RadioButton's
 *
 * @version    1.0
 * @package    widget_gtk
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TRadioGroup extends GtkHbox
{
    private $radios;
    private $wname;
    private $items;
    private $validations;
    
    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct($name)
    {
        parent::__construct(FALSE);
        parent::set_border_width(0);
        $this->wname = $name;
        $this->wrapper = new GtkHBox;
        parent::add($this->wrapper);
        $this->setLayout('vertical');
        
        $this->validations = array();
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
     * Define the active option
     * @param  $value  option index
     */
    public function setValue($value)
    {
        if (isset($this->radios[$value]))
        {
            $this->radios[$value]->set_active(TRUE);
        }
    }
    
    /**
     * Return the active option
     */
    public function getValue()
    {
        foreach ($this->radios as $key => $radio)
        {
            if ($radio->get_active())
            {
                return $key;
            }
        }
    }
    
    /**
     * Define the direction of the options
     * @param $direction Direction of the RadioButton (vertical, horizontal)
     */
    public function setLayout($layout = 'horizontal')
    {
        parent::remove($this->wrapper);
        if ($layout == 'horizontal')
            $this->wrapper = new GtkHBox(FALSE, 0);
        else
            $this->wrapper = new GtkVBox(FALSE, 0);
        parent::add($this->wrapper);
        
        // keep items even removing the container
        if (is_array($this->items))
        {
            $this->addItems($this->items);
        }
    }
    
    /**
     * Add Items to the RadioButton
     * @param $items An array containing the RadioButton options
     */
    public function addItems($items)
    {
        $first = NULL;
        $this->items = $items;
        foreach ($items as $index=>$label)
        {
            $this->radios[$index] = new GtkRadioButton($first, $label);
            if (!$first)
            {
                $first = $this->radios[$index];
            }
            $this->wrapper->pack_start($this->radios[$index], FALSE, FALSE, 0);
        }
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