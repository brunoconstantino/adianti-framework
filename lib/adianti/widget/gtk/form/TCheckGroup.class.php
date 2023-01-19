<?php
/**
 * A group of CheckButton's
 *
 * @version    1.0
 * @package    widget_gtk
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TCheckGroup extends GtkHbox
{
    private $checks;
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
        $this->validations = array();
        parent::add($this->wrapper);
        $this->setLayout('vertical');
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
     * Define the direction of the CheckButtons
     * @param $direction A string 'vertical' or 'horizontal'
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
     * Add items to the check group
     * @param $items An indexed array containing the options
     */
    public function addItems($items)
    {
        $this->items = $items;
        foreach ($items as $index=>$label)
        {
            $this->checks[$index] = new GtkCheckButton($label);
            $this->wrapper->pack_start($this->checks[$index], FALSE, FALSE, 0);
        }
    }
    
    /**
     * Define wich check button will be active
     * @param $items An array indicating wich check buttons will be active
     */
    public function setValue($items)
    {
        if ($this->checks)
        {
            foreach ($this->checks as $key => $check)
            {
                if (in_array($key, $items))
                {
                    $check->set_active(TRUE);
                }
            }
        }
    }
    
    /**
     * Returns the current active radio button
     */
    public function getValue()
    {
        $returns = array();
        foreach ($this->checks as $key => $radio)
        {
            if ($radio->get_active())
            {
                $returns[] = $key;
            }
        }
        return $returns;
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