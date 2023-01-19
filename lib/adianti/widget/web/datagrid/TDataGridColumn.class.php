<?php
/**
 * Representes a DataGrid column
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage datagrid
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TDataGridColumn
{
    private $name;
    private $label;
    private $align;
    private $width;
    private $action;
    private $transformer;
    
    /**
     * Class Constructor
     * @param  $name  = Name of the column in the database
     * @param  $label = Text label that will be shown in the header
     * @param  $align = Column align (left, center, right)
     * @param  $width = Column Width (pixels)
     */
    public function __construct($name, $label, $align, $width = NULL)
    {
        $this->name  = $name;
        $this->label = $label;
        $this->align = $align;
        $this->width = $width;
    }
    
    /**
     * Returns the database column's name
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Returns the column's label
     */
    public function getLabel()
    {
        return $this->label;
    }
    
    /**
     * Set the column's label
     * @param $label column label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }
    
    /**
     * Returns the column's align
     */
    public function getAlign()
    {
        return $this->align;
    }
    
    /**
     * Returns the column's width
     */
    public function getWidth()
    {
        return $this->width;
    }
    
    /**
     * Define the action to be executed when
     * the user clicks over the column header
     * @param $action   A TAction object
     */
    public function setAction(TAction $action)
    {
        $this->action = $action;
    }
    
    /**
     * Returns the action defined by set_action() method
     * @return the action to be executed when the
     * user clicks over the column header
     */
    public function getAction()
    {
        // verify if the column has an actions
        if ($this->action)
        {
            return $this->action->serialize();
        }
    }
    
    /**
     * Define a callback function to be applyed over the column's data
     * @param $callback  A function name of a method of an object
     */
    public function setTransformer($callback)
    {
        $this->transformer = $callback;
    }

    /**
     * Returns the callback defined by the setTransformer()
     */
    public function getTransformer()
    {
        return $this->transformer;
    }
}
?>