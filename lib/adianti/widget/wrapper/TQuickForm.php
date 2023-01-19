<?php
Namespace Adianti\Widget\Wrapper;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Control\TAction;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Container\TTable;
use Adianti\Widget\Container\THBox;
use Adianti\Validator\TFieldValidator;
use Adianti\Validator\TRequiredValidator;

/**
 * Create quick forms for input data with a standard container for elements
 *
 * @version    2.0
 * @package    widget
 * @subpackage wrapper
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TQuickForm extends TForm
{
    protected $fields; // array containing the form fields
    protected $name;   // form name
    protected $actionButtons;
    private   $table;
    private   $action_row;
    private   $has_action;
    
    /**
     * Class Constructor
     * @param $name Form Name
     */
    public function __construct($name = 'my_form')
    {
        parent::__construct($name);
        
        // creates a table
        $this->table = new TTable;
        $this->has_action = FALSE;
        
        // add the table to the form
        parent::add($this->table);
    }
    
    /**
     * Returns the inner table
     */
    public function getTable()
    {
        return $this->table;
    }
    
    /**
     * Intercepts whenever someones assign a new property's value
     * @param $name     Property Name
     * @param $value    Property Value
     */
    public function __set($name, $value)
    {
        if ($name == 'class')
        {
            $this->table->{'width'} = '100%';
        }
        
        if (method_exists('TForm', '__set'))
        {
            parent::__set($name, $value);
        }
    }
    
    /**
     * Returns the form container
     */
    public function getContainer()
    {
        return $this->table;
    }
    
    /**
     * Add a form title
     * @param $title     Form title
     */
    public function setFormTitle($title)
    {
        // add the field to the container
        $row = $this->table->addRow();
        $row->{'class'} = 'tformtitle';
        $this->table->{'width'} = '100%';
        $cell = $row->addCell( new TLabel($title) );
        $cell->{'colspan'} = 2;
    }
    
    /**
     * Add a form field
     * @param $label     Field Label
     * @param $object    Field Object
     * @param $size      Field Size
     * @param $validator Field Validator
     */
    public function addQuickField($label, AdiantiWidgetInterface $object, $size = 200, TFieldValidator $validator = NULL)
    {
        $object->setSize($size, $size);
        parent::addField($object);
        
        // add the field to the container
        $row = $this->table->addRow();
        
        if ($validator instanceof TRequiredValidator)
        {
            $label_field = new TLabel($label . '(*)');
            $label_field->setFontColor('#FF0000');
        }
        else
        {
            $label_field = new TLabel($label);
        }
        if ($object instanceof THidden)
        {
            $row->addCell( '' );
        }
        else
        {
            $row->addCell( $label_field );
        }
        $row->addCell( $object );
        
        if ($validator)
        {
            $object->addValidation($label, $validator);
        }
        
        return $row;
    }
    
    /**
     * Add a form field
     * @param $label     Field Label
     * @param $objects   Array of Objects
     * @param $required  Boolean TRUE if required
     */
    public function addQuickFields($label, $objects, $required = FALSE)
    {
        // add the field to the container
        $row = $this->table->addRow();
        
        if ($required)
        {
            $label_field = new TLabel($label . '(*)');
            $label_field->setFontColor('#FF0000');
        }
        else
        {
            $label_field = new TLabel($label);
        }
        
        $row->addCell( $label_field );
        
        $hbox = new THBox;
        foreach ($objects as $object)
        {
            parent::addField($object);
            $hbox->add($object);
        }
        $row->addCell( $hbox );
        
        return $row;
    }
    
    /**
     * Add a form action
     * @param $label  Action Label
     * @param $action TAction Object
     * @param $icon   Action Icon
     */
    public function addQuickAction($label, TAction $action, $icon = 'ico_save.png')
    {
        $name   = strtolower(str_replace(' ', '_', $label));
        $button = new TButton($name);
        parent::addField($button);
        
        // define the button action
        $button->setAction($action, $label);
        $button->setImage($icon);
        
        if (!$this->has_action)
        {
            // creates the action table
            $actions = new TTable;
            $this->action_row = $actions->addRow();
            
            $row  = $this->table->addRow();
            $row->{'class'} = 'tformaction';
            $cell = $row->addCell($actions);
            $cell->colspan = 2;
        }
        
        // add cell for button
        $this->action_row->addCell($button);
        
        $this->has_action = TRUE;
        $this->actionButtons[] = $button;
        
        return $button;
    }
    
    /**
     * Clear actions row
     */
    public function delActions()
    {
        $this->action_row->clearChildren();
    }
    
    /**
     * Return an array with action buttons
     */
    public function getActionButtons()
    {
        return $this->actionButtons;
    }
    
    /**
     * Add a row
     */
    public function addRow()
    {
        return $this->table->addRow();
    }
}
