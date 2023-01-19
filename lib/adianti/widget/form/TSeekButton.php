<?php
Namespace Adianti\Widget\Form;

use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Control\TAction;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TField;
use Adianti\Widget\Util\TImage;

use Adianti\Core\AdiantiCoreTranslator;

use Exception;
use Gtk;
use GtkHBox;
use GtkEntry;
use GtkObject;

/**
 * Record Lookup Widget: Creates a lookup field used to search values from associated entities
 *
 * @version    2.0
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TSeekButton extends TField implements AdiantiWidgetInterface
{
    private $action;
    private $auxiliar;
    private $entry;
    private $btn;
    private $validations;
    private $useOutEvent;
    protected $widget;
    protected $formName;
    
    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->widget = new GtkHBox;
        parent::add($this->widget);
        
        $this->entry = new GtkEntry;
        $this->btn   = new TButton('find');
        $this->btn->set_image(new TImage('lib/adianti/images/ico_find.png'));
        $this->btn->set_relief(Gtk::RELIEF_NONE);
        
        $this->useOutEvent = TRUE;
        $this->validations = array();
        
        $this->widget->pack_start($this->entry, false, false);
        $this->widget->pack_start($this->btn, false, false);
    }
    
    /**
     * Define the widget's content
     * @param  $value  widget's content
     */
    public function setValue($value)
    {
        $this->entry->set_text($value);
    }
    
    /**
     * Return the widget's content
     * @return A string containing the widget's content
     */
    public function getValue()
    {
        return $this->entry->get_text();
    }
    
    /**
     * Define the Field's size
     * @param $width Field's width in pixels
     */
    public function setSize($width, $height = NULL)
    {
        $this->entry->set_size_request($width, 24);
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
     * Define if the widget is editable
     * @param $boolean A boolean
     */
    public function setEditable($editable)
    {
        $this->entry->set_sensitive($editable);
        $this->btn->set_sensitive($editable);
    }
    
    /**
     * Return if the widget is editable
     */
    public function getEditable()
    {
        return $this->entry->get_sensitive();
    }
    
    /**
     * Define it the out event will be fired
     */
    public function setUseOutEvent($bool)
    {
        $this->useOutEvent = $bool;
    }
    
    /**
     * Define the action for the SeekButton
     * @param $action Action taken when the user clicks over the Seek Button (A TAction object)
     */
    public function setAction(TAction $action)
    {
        $callback=$action->getAction();
        $this->btn->setAction($action, '');
        $param=array();
        if (is_array($callback))
        {
            $classname = get_class($callback[0]);
            if (in_array($classname, array('TStandardSeek', 'Adianti\Base\TStandardSeek')))
            {
                $param['key'] = 3;
                $param['parent'] = $action->getParameter('parent');
                $param['database'] = $action->getParameter('database');
                $param['model'] =  $action->getParameter('model');
                $param['display_field'] = $action->getParameter('display_field');
                $param['receive_key'] =   $action->getParameter('receive_key');
                $param['receive_field'] = $action->getParameter('receive_field');
            }
        }
        
        if ($this->useOutEvent)
        {
            // get_text aqui não é on-the-fly, tem que chamar um método na hora do evento
            $this->entry->connect_simple('focus-out-event', array($this, 'onBlur'), $callback, $param);
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
        $this->entry->connect_after('focus-out-event', array($this, 'onExecuteExitAction'));
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
     * When the user leaves the input, collects the text and executes the callback
     * @param $callback = Callback to be executed
     * @param $param    = array of parameters
     * @ignore-autocomplete on
     */
    public function onBlur($callback, $param)
    {
        if (is_callable($callback))
        {
            $param['key'] = $this->entry->get_text();
            call_user_func(array($callback[0], 'onSelect'), $param);
        }
    }
    
    /**
     * Define an auxiliar field
     * @param $object any TField object
     */
    public function setAuxiliar(GtkObject $object)
    {
        $this->auxiliar = $object;
        $this->widget->pack_start($this->auxiliar, false, false);
    }
}
