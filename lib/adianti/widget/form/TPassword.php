<?php
Namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Form\TField;
use Adianti\Widget\Form\TForm;
use Adianti\Control\TAction;

use Exception;
use Gtk;
use GtkEntry;

/**
 * Password Widget
 *
 * @version    2.0
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TPassword extends TField implements AdiantiWidgetInterface
{
    protected $widget;
    protected $formName;
    
    /**
     * Class Constructor
     * @param  $name Field Name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        
        $this->widget = new GtkEntry;
        parent::add($this->widget);
        $this->setSize(200);
        $this->widget->set_visibility(FALSE);
    }
    
    /**
     * Define the field's value
     * @param $value A string containing the field's value
     */
    public function setValue($value)
    {
        $this->widget->set_text($value);
    }
    
    /**
     * Returns the field's value
     */
    public function getValue()
    {
        return $this->widget->get_text();
    }
    
    /**
     * Define the widget's size
     * @param $size Widget's size in pixels
     */
    public function setSize($width, $height = NULL)
    {
        $this->widget->set_size_request($width, 24);
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
        $this->widget->connect_after('focus-out-event', array($this, 'onExecuteExitAction'));
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
     * Define max length
     * @param  $length Max length
     */
    public function setMaxLength($length)
    {
        if ($length > 0)
        {
            $this->widget->set_max_length($length);
        }
    }
}
