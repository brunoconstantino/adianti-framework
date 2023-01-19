<?php
Namespace Adianti\Widget\Form;

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Form\TEntry;

use Gtk;
use GtkHBox;
use GtkVBox;
use GtkButton;
use GtkImage;
use GtkWindow;
use GtkCalendar;

/**
 * DataPicker Widget
 *
 * @version    2.0
 * @package    widget
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TDate extends TField implements AdiantiWidgetInterface
{
    private $calendar;  // GtkCalendar
    private $mask;
    private $month;
    private $year;
    private $actionButton;
    protected $entry;
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
        
        $this->mask = 'yyyy-mm-dd';
        
        // creates the entry field
        $this->entry = new TEntry($name);
        $this->entry->setSize(200);
        $this->setMask($this->mask);
        $this->widget->add($this->entry);
        
        // creates a button with a calendar image
        $button = new GtkButton;
        $button->set_relief(GTK::RELIEF_NONE);
        $imagem = GtkImage::new_from_file('lib/adianti/images/tdate-gtk.png');
        $button->set_image($imagem);
        $this->actionButton = $button;
        // define the button's callback
        $button->connect_simple('clicked', array($this, 'onCalendar'));
        $this->widget->add($button);
        
        // creates the calendar window
        $this->popWindow = new GtkWindow(Gtk::WINDOW_POPUP);
        
        // creates the calendar
        $this->calendar = new GtkCalendar;
        // define the action when the user selects a date
        $this->calendar->connect_simple('day-selected-double-click', array($this, 'onSelectDate'));
        
        $this->month = new TCombo('tdate-month');
        $this->month->addItems(array(AdiantiCoreTranslator::translate('January'),AdiantiCoreTranslator::translate('February'),AdiantiCoreTranslator::translate('March'),AdiantiCoreTranslator::translate('April'),AdiantiCoreTranslator::translate('May'),AdiantiCoreTranslator::translate('June'),AdiantiCoreTranslator::translate('July'),AdiantiCoreTranslator::translate('August'),AdiantiCoreTranslator::translate('September'),AdiantiCoreTranslator::translate('October'),AdiantiCoreTranslator::translate('November'),AdiantiCoreTranslator::translate('December')));
        $this->month->setCallback(array($this, 'onChangeMonth'));
        $this->month->setSize(70);
        
        for ($n=date('Y')-10; $n<=date('Y')+10; $n++)
        {
            $years[$n] = $n;
        }
        
        $this->year = new TCombo('tdate-year');
        $this->year->addItems($years);
        $this->year->setCallback(array($this, 'onChangeMonth'));
        $this->year->setSize(70);
        
        $hbox = new GtkHBox;
        $hbox->pack_start($this->month);
        $hbox->pack_start($this->year);
        
        $bt_today = new GtkButton(AdiantiCoreTranslator::translate('Today'));
        $bt_close = new GtkButton(AdiantiCoreTranslator::translate('Close'));
        $bt_today->connect_simple('clicked', array($this, 'selectToday'));
        $inst = $this->popWindow;
        $bt_close->connect_simple('clicked', array($inst, 'hide'));
        
        $hbox2 = new GtkHBox;
        $hbox2->pack_start($bt_today);
        $hbox2->pack_start($bt_close);
        
        $vbox = new GtkVBox;
        $vbox->pack_start($hbox, FALSE, FALSE);
        $vbox->pack_start($this->calendar);
        $vbox->pack_start($hbox2, FALSE, FALSE);
        
        // shows the window
        $this->popWindow->add($vbox);
    }
    
    /**
     * Define the current date
     * @param $value A string containing a date
     */
    public function setValue($value)
    {
        $this->entry->setValue($value);
    }
    
    /**
     * Returns the current date
     */
    public function getValue()
    {
        return $this->entry->getValue();
    }
    
    /**
     * Define the widget's size
     * @param $size Widget's size in pixels
     */
    public function setSize($width, $height = NULL)
    {
        $this->entry->setSize($width);
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
        $this->entry->setExitAction($action);
    }
    
    /**
     * Define the name of the form to wich the button is attached
     * @param $name    A string containing the name of the form
     * @ignore-autocomplete on
     */
    public function setFormName($name)
    {
        parent::setFormName($name);
        $this->entry->setFormName($name);
    }
    
    /**
     * Define if the widget is editable
     * @param $boolean  A boolean indicating if the widget is editable
     */
    public function setEditable($editable)
    {
        $this->entry->setEditable($editable);
        $this->actionButton->set_sensitive($editable);
    }
    
    /**
     * Register a tip
     * @param $text Tooltip Text
     */
    function setTip($text)
    {
        $this->entry->setTip($text);
    }
    
    /**
     * Opens the callendar, allowing the date selection
     * @ignore-autocomplete on
     */
    public function onCalendar()
    {
        $position = $this->window->get_toplevel()->get_position();
        $pointer  = $this->window->get_toplevel()->get_pointer();
        
        $x = $position[0] + $pointer[0];
        $y = $position[1] + $pointer[1];
        
        $value = $this->getValue();
        if ($value)
        {
            $dd = strpos($this->mask, 'dd');
            $mm = strpos($this->mask, 'mm');
            $yy = strpos($this->mask, 'yyyy');
            
            $day   = substr($value, $dd, 2);
            $month = substr($value, $mm, 2) -1;
            $year  = substr($value, $yy, 4);
        }
        else
        {
            $day   = date('d');
            $month = date('m')-1;
            $year  = date('Y');
        }
        
        $this->month->setValue($month);
        $this->year->setValue((int)$year);
        
        $this->calendar->select_day($day);
        $this->calendar->select_month($month, $year);
        
        $this->popWindow->set_uposition($x, $y);
        $this->popWindow->show_all();
    }
    
    /**
     * Executed when the user selects a date
     * @ignore-autocomplete on
     */
    public function onSelectDate()
    {
        // get the selected date
        $date  = $this->calendar->get_date();
        $day   = str_pad($date[2], 2, '0', STR_PAD_LEFT);
        $month = str_pad($date[1] +1, 2, '0', STR_PAD_LEFT);
        $year  = $date[0];
        
        // translate the selected date using the mask
        $return = $this->mask;
        $return = str_replace('dd',   $day,   $return);
        $return = str_replace('mm',   $month, $return);
        $return = str_replace('yyyy', $year,  $return);
        
        // put the selected date in the entry field
        $this->entry->setValue($return);
        
        if (isset($this->exitAction))
        {
            $this->entry->onExecuteExitAction();
        }
        
        // fecha janela do calendário
        $this->popWindow->hide();
    }
    
    /**
     * Define the field's mask
     * @param $mask  Mask for the field (dd-mm-yyyy)
     */
    public function setMask($mask)
    {
        $this->mask = $mask;
        
        $newmask = $this->mask;
        $newmask = str_replace('dd',   '99',   $newmask);
        $newmask = str_replace('mm',   '99',   $newmask);
        $newmask = str_replace('yyyy', '9999', $newmask);
        $this->entry->setMask($newmask);
    }
    
    /**
     * Convert a date to format yyyy-mm-dd
     * @param $date = date in format dd/mm/yyyy
     */
    public function date2us($date)
    {
        if ($date)
        {
            // get the date parts
            $day  = substr($date,0,2);
            $mon  = substr($date,3,2);
            $year = substr($date,6,4);
            return "{$year}-{$mon}-{$day}";
        }
    }
    
    /**
     * Convert a date to format dd/mm/yyyy
     * @param $date = date in format yyyy-mm-dd
     */
    public function date2br($date)
    {
        if ($date)
        {
            // get the date parts
            $year = substr($date,0,4);
            $mon  = substr($date,5,2);
            $day  = substr($date,8,4);
            return "{$day}/{$mon}/{$year}";
        }
    }
    
    /**
     * Run when the user changes the month combo
     * @ignore-autocomplete on
     */
    public function onChangeMonth()
    {
        $month = $this->month->getValue();
        $year  = $this->year->getValue();
        
        if ($month!==NULL AND $year!==NULL)
        {
            $this->calendar->select_month($month, $year);
        }
    }
    
    /**
     * Select today in the calendar
     * @ignore-autocomplete on
     */
    public function selectToday()
    {
        $this->calendar->select_day(1); // yes, there's a reason for this
        $this->calendar->select_month(date('m')-1, date('Y'));
        $this->calendar->select_day(date('d'));
        
        $this->month->setValue(date('m')-1);
        $this->year->setValue(date('Y'));
    }
}
