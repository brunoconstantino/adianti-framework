<?php
Namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Control\TAction;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TField;

use Adianti\Core\AdiantiCoreTranslator;

use Exception;
use Gtk;
use GtkSpinButton;

/**
 * Spinner Widget (also known as spin button)
 *
 * @version    2.0
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TSpinner extends TField implements AdiantiWidgetInterface
{
    protected $widget;
    protected $formName;
    protected $exitAction;
    
    /**
     * Class Constructor
     * @param  $name Field Name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->widget = new GtkSpinButton;
        parent::add($this->widget);
        $this->setSize(200);
    }
    
    /**
     * Define the widget's content
     * @param  $value  widget's content
     */
    public function setValue($value)
    {
        $this->widget->set_value($value);
    }

    /**
     * Return the widget's content
     */
    public function getValue()
    {
        return $this->widget->get_value();
    }
    
    /**
     * Define the Field's size
     * @param $width Field's width in pixels
     */
    public function setSize($width, $height = NULL)
    {
        $this->widget->set_size_request($width, -1);
    }
    
    /**
     * Define a field property
     * @param $name  Property Name
     * @param $value Property Value
     */
    public function setProperty($name, $value, $replace = TRUE)
    {
        if ($name == 'readonly')
        {
            $this->widget->set_editable(false);
        }
    }
    
    /**
     * Return a field property
     * @param $name  Property Name
     * @param $value Property Value
     */
    public function getProperty($name)
    {
        if ($name == 'readonly')
        {
            return $this->widget->get_editable();
        }
    }
    
    /**
     * Define the action to be executed when the user leaves the form field
     * @param $action TAction object
     */
    function setExitAction(TAction $action)
    {
        if ($action->isStatic())
        {
            $this->exitAction = $action;
        }
        else
        {
            $string_action = $action->toString();
            throw new Exception(AdiantiCoreTranslator::translate('Action (^1) must be static to be used in ^2', $string_action, __METHOD__));
        }
        $this->widget->connect_after('value-changed', array($this, 'onExecuteExitAction'));
    }
    
    /**
     * Execute the exit action
     */
    public function onExecuteExitAction()
    {
        if (!TForm::getFormByName($this->formName) instanceof TForm)
        {
            throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $this->wname, 'TForm::setFields()') );
        }
        
        if (isset($this->exitAction) AND $this->exitAction instanceof TAction)
        {
            $callback = $this->exitAction->getAction();
            $param = (array) TForm::retrieveData($this->formName);
            call_user_func($callback, $param);
        }
    }
    
    /**
     * Define the field's range
     * @param $min Minimal value
     * @param $max Maximal value
     * @param $step Step value
     */
    public function setRange($min, $max, $step)
    {
        $this->widget->set_increments($step, $step*10);
        $this->widget->set_range($min, $max);
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
                $field->setValue(0);
            }
        }
    }
}
