<?php
Namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Control\TAction;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Form\TField;

use Exception;
use Gtk;
use GObject;
use GtkComboBox;
use GtkListStore;

/**
 * ComboBox Widget
 *
 * @version    2.0
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TCombo extends TField implements AdiantiWidgetInterface
{
    private $model;
    private $iters;
    protected $changeAction;
    protected $widget;
    protected $formName;
    private   $defaultOption;
    
    /**
     * Class Constructor
     * @param  $name widget's name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        
        $this->widget = GtkComboBox::new_text();
        parent::add($this->widget);
        $this->defaultOption = '';
        
        // create the combo model
        $this->model = new GtkListStore(GObject::TYPE_STRING, GObject::TYPE_STRING);
        $this->widget->set_model($this->model);
        
        $this->setSize(200);
    }
    
    /**
     * Define wich item will be shown
     * @param $value  The item index
     */
    public function setValue($value)
    {
        if (isset($this->iters[$value]))
        {
            $this->widget->set_active_iter($this->iters[$value]);
        }
        else
        {
            $this->widget->set_active(0);
        }
    }
    
    /**
     * Return the current item showed
     */
    public function getValue()
    {
        $iter  = $this->widget->get_active_iter();
        if ($iter)
        {
            $model = $this->widget->get_model();
            
            $valor = $model->get_value($iter, 1);
            return $valor;
        }
    }
    
    /**
     * Define the Field's width
     * @param $width Field's width in pixels
     */
    public function setSize($width, $height = NULL)
    {
        $this->widget->set_size_request($width, -1);
    }
    
    /**
     * Not implemented
     */
    public function setProperty($name, $value, $replace = TRUE)
    {}
    
    /**
     * Not implemented
     */
    public function getProperty($name)
    {}
    
    /**
     * Clear the combo
     */
    public function clear()
    {
        $this->model->clear();
    }
    
    /**
     * Add items to the combo box
     * @param $items An indexed array containing the options
     */
    public function addItems($items)
    {
        if (is_array($items))
        {
            if ($this->defaultOption !== FALSE)
            {
                $this->model->append(array('', $this->defaultOption));
            }
            foreach ($items as $key=>$value)
            {
                $this->iters[$key] = $this->model->append(array($value, $key));
            }
        }
    }
    
    /**
     * Define the action to be executed when the user changes the combo
     * @param $action TAction object
     */
    function setChangeAction(TAction $action)
    {
        if ($action->isStatic())
        {
            $this->changeAction = $action;
        }
        else
        {
            $string_action = $action->toString();
            throw new Exception(AdiantiCoreTranslator::translate('Action (^1) must be static to be used in ^2', $string_action, __METHOD__));
        }
        
        $this->widget->connect('changed', array($this, 'onExecuteExitAction'));
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
        
        if (isset($this->changeAction) AND $this->changeAction instanceof TAction)
        {
            $callback = $this->changeAction->getAction();
            $param = (array) TForm::retrieveData($this->formName);
            call_user_func($callback, $param);
        }
    }
    
    /**
     * Reload combobox items after it is already shown
     * @param $formname form name (used in gtk version)
     * @param $name field name
     * @param $items array with items
     */
    public static function reload($formname, $name, $items)
    {
        $form = TForm::getFormByName($formname);
        $combo = $form->getField($name);
        $combo->clear();
        $combo->addItems($items);
    }
    
    /**
     * Define a callback for change
     * @param $callback PHP valid callback
     * @ignore-autocomplete on
     */
    public function setCallback($callback)
    {
        $this->widget->connect('changed', $callback);
    }
    
    /**
     * Define the combo default option value
     * @param $option option value
     */
    public function setDefaultOption($option)
    {
        $this->defaultOption = $option;
    }
}
