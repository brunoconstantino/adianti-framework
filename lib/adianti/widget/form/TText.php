<?php
Namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Control\TAction;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TField;
use Adianti\Core\AdiantiCoreTranslator;

use Exception;
use Gtk;
use GtkScrolledWindow;
use GtkTextView;

/**
 * Text Widget (also known as Memo)
 *
 * @version    2.0
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TText extends TField implements AdiantiWidgetInterface
{
    protected $widget;
    protected $formName;
    protected $exitAction;
    
    /**
     * Class Constructor
     * @param $name Widet's name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        
        $this->widget = new GtkScrolledWindow;
        $this->widget->set_size_request(200, -1);
        parent::add($this->widget);
        
        $this->textview = new GtkTextView;
        $this->textview->set_wrap_mode(Gtk::WRAP_WORD);
        $this->textbuffer = $this->textview->get_buffer();
        $this->widget->add($this->textview);
    }
    
    /**
     * Define the widget's content
     * @param  $value  widget's content
     */
    public function setValue($value)
    {
        $first = $this->textbuffer->get_start_iter();
        $end   = $this->textbuffer->get_end_iter();
        $this->textbuffer->delete($first, $end);
        
        // Insert the content in the text buffer
        $this->textbuffer->insert_at_cursor($value);
    }

    /**
     * Return the widget's content
     */
    public function getValue()
    {
        $first = $this->textbuffer->get_start_iter();
        $end   = $this->textbuffer->get_end_iter();
        return $this->textbuffer->get_text($first, $end);
    }
    
    /**
     * Define the widget's size
     * @param $size Widget's size in pixels
     */
    public function setSize($width, $height = -1)
    {
        $this->widget->set_size_request($width, $height);
    }
    
    /**
     * Returns the size
     * @return array(width, height)
     */
    public function getSize()
    {
        $size = $this->widget->get_size_request();
        return array( $size[0], $size[1] );
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
        $this->textview->connect_after('focus-out-event', array($this, 'onExecuteExitAction'));
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
}
