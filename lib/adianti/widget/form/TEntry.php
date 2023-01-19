<?php
Namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Form\TField;
use Adianti\Control\TAction;
use Adianti\Core\AdiantiCoreTranslator;

use Exception;
use Gtk;
use GObject;
use GtkEntry;
use GtkListStore;
use GtkEntryCompletion;

/**
 * Entry Widget
 *
 * @version    2.0
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TEntry extends TField implements AdiantiWidgetInterface
{
    private $mask;
    private $chars;
    private $handler;
    private $validations;
    protected $widget;
    protected $formName;
    protected $exitAction;
    
    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct($name)
    {
        parent::__construct($name);
        
        $this->widget = new GtkEntry;
        parent::add($this->widget);
        $this->setSize(200);
        
        $this->chars = array('-', '_', '.', '/', '\\', ':',
                             '|', '(', ')', '[', ']', '{', '}');
        
        // Connecting 'changed' signal to check the typed chars.
        $this->handler = $this->widget->connect_after('changed', array($this, 'onChanged'));
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
    
    /**
     * Define the field's mask
     * @param $mask A mask for input data
     */
    public function setMask($mask)
    {
        $this->widget->set_max_length(strlen(trim($mask)));
        $this->mask = $mask;
    }
    
    /**
     * Not implemented in Gtk
     * Just for compatibility purposes
     */
    public function setNumericMask($decimals, $decimalsSeparator, $thousandSeparator)
    {
        return FALSE;
    }
    
    /**
     * Changes the Entry contents without fire 'changed' signal
     * @param string $text the new text
     * @ignore-autocomplete on
     */
    public function Set($text)
    {
        // turn off the signal
        $this->widget->disconnect($this->handler);
        $this->widget->set_text($text);
        
        // cursor to the end
        $this->widget->select_region(-1,-1);
        // turn on the signal
        $this->handler = $this->widget->connect_after('changed', array($this, 'onChanged'));
    }
   
    /**
     * whenever the user types something
     * the content is validated according to the mask
     * @ignore-autocomplete on
     */
    public function onChanged()
    {
        if ($this->mask)
        {
            $text = $this->widget->get_text();
            // remove the separadtors
            $text = $this->unMask($text);
            $len  = strlen(trim($text));
            
            // apply the mask
            $new  = $this->Mask($this->mask, $text);
            
            // schedule the new content.
            Gtk::timeout_add(1, array($this, 'Set'), $new);
            Gtk::timeout_add(1, array($this, 'validateMask'));
        }
    }
    
    /**
     * Validate the content of GtkEntry
     * @ignore-autocomplete on
     */
    public function validateMask()
    {
        $valid = FALSE;
        $text = $this->widget->get_text();
        $mask = $this->mask;
        $len  = strlen($text);
        
        $text_char = substr($text, $len-1, 1);
        $mask_char = substr($mask, $len-1, 1);
        
        // compare the typed character with the mask
        if ($mask_char == '9')
            $valid = preg_match("/([0-9])/", $text_char);
        elseif ($mask_char == 'a')
            $valid = preg_match("/([a-z])/", $text_char);
        elseif ($mask_char == 'A')
            $valid = preg_match("/([A-Z])/", $text_char);
        elseif ($mask_char == 'X')
            $valid = (preg_match("/([a-z])/", $text_char) or
                     preg_match("/([A-Z])/", $text_char) or
                     preg_match("/([0-9])/", $text_char));
        
        // if not valid, remove
        if (!$valid)
        {
            $this->Set(substr($text, 0, -1));
        }
    }
    
    /**
     * put the typed content in the mask format
     * @param string $mask the mask
     * @param string $text the content
     * @ignore-autocomplete on
     */
    private function Mask($mask, $text)
    {
        $z = 0;
        $result = '';
        // run through the mask chars
        for ($n=0; $n < strlen($mask); $n++)
        {
            $mask_char = substr($mask, $n, 1);
            $text_char = substr($text, $z, 1);
            
            // check when has to concatenate with the separator
            if (in_array($mask_char, $this->chars))
            {
                if ($z<strlen($text))
                    $result .= $mask_char;
            }
            else
            {
                $result .= $text_char;
                $z ++;
            }
            
        }
        return $result;
    }
    
    /**
     * removes the mask from text
     * @param string $text the content
     * @ignore-autocomplete on
     */
    private function unMask($text)
    {
        $result ='';
        // run through the content
        for ($n=0; $n <= strlen($text); $n++)
        {
            $char = substr($text, $n, 1);
            // check if it's a separator
            if (!in_array($char, $this->chars))
            {
                $result .= $char;
            }
        }
        return $result;
    }
    
    /**
     * Define options for completion
     * @param $options array of options for completion
     */
    function setCompletion($options)
    {
        $store = new GtkListStore(GObject::TYPE_STRING);
        
        if (is_array($options))
        {
            foreach ($options as $option)
            {
                $store->append(array($option));
            }
        }
        
        $completion = new GtkEntryCompletion;
        $completion->set_model($store);
        $completion->set_text_column(0);
        $this->widget->set_completion($completion);
    }
}
