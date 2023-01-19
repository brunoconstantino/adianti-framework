<?php
Namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Control\TAction;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TField;

use Exception;
use Gtk;
use GtkHbox;
use GtkVbox;
use GtkRadioButton;

/**
 * A group of RadioButton's
 *
 * @version    2.0
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TRadioGroup extends TField implements AdiantiWidgetInterface
{
    private $radios;
    private $items;
    private $changeAction;
    private $validations;
    private $layout;
    protected $widget;
    protected $formName;
    
    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->widget = new GtkHbox(FALSE);
        parent::add($this->widget);
        
        $this->widget->set_border_width(0);
        $this->setLayout('vertical');
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
     * clearSelection
     */
    public function clearSelection()
    {
        if ($this->radios)
        {
            foreach ($this->radios as $key => $radio)
            {
                $radio->set_active(FALSE);
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
        
        if ($this->radios)
        {
            foreach ($this->radios as $key => $radio)
            {
                $radio->connect('clicked', array($this, 'onExecuteExitAction'));
            }
        }
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
     * Define the direction of the options
     * @param $direction Direction of the RadioButton (vertical, horizontal)
     */
    public function setLayout($layout = 'horizontal')
    {
        $this->layout = $layout;
        parent::remove($this->widget);
        if ($layout == 'horizontal')
            $this->widget = new GtkHBox(FALSE, 0);
        else
            $this->widget = new GtkVBox(FALSE, 0);
        parent::add($this->widget);
        
        // keep items even removing the container
        if (is_array($this->items))
        {
            $value = $this->getValue();
            $this->addItems($this->items);
            $this->setValue($value);
        }
    }
    
    /**
     * Get the direction (vertical or horizontal)
     */
    public function getLayout()
    {
        return $this->layout;
    }
    
    /**
     * Add Items to the RadioButton
     * @param $items An array containing the RadioButton options
     */
    public function addItems($items)
    {
        if (is_array($items))
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
                $this->widget->pack_start($this->radios[$index], FALSE, FALSE, 0);
            }
        }
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
                $field->clearSelection();
            }
        }
    }
    
    /**
     * Register a tip
     * @param $text Tooltip Text
     */
    function setTip($text)
    {
        if ($this->radios)
        {
            foreach ($this->radios as $key => $radio)
            {
                if (method_exists($radio, 'set_tooltip_text'))
                {
                    $radio->set_tooltip_text($text);
                }
                else
                {
                    $tooltip = TooltipSingleton::getInstance();
                    $tooltip->set_tip($radio, $text);
                }
            }
        }
    }
}
