<?php
/**
 * DataPicker Widget
 *
 * @version    1.0
 * @package    widget_gtk
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TDate extends GtkHBox
{
    private $entry;     // entry field
    private $calendar;  // GtkCalendar
    private $wname;
    private $mask;
    private $month;
    private $year;
    private $validations;
    
    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct($name)
    {
        parent::__construct();
        
        $this->wname = $name;
        $this->mask = 'dd/mm/yyyy';
        $this->validations = array();
        
        // creates the entry field
        $this->entry    = new GtkEntry;
        $this->entry->set_size_request(200, 24);
        
        // avoid user to type
        $this->entry->set_editable(false);
        $this->entry->set_sensitive(FALSE);
        parent::add($this->entry);
        
        // creates a button with a calendar image
        $button = new GtkButton;
        $button->set_relief(GTK::RELIEF_NONE);
        $imagem = GtkImage::new_from_file('lib/adianti/images/tdate-gtk.png');
        $button->set_image($imagem);
        
        // define the button's callback
        $button->connect_simple('clicked', array($this, 'onCalendar'));
        parent::add($button);
        
        // creates the calendar window
        $this->popWindow = new GtkWindow(Gtk::WINDOW_POPUP);
        
        // creates the calendar
        $this->calendar = new GtkCalendar;
        // define the action when the user selects a date
        $this->calendar->connect_simple('day-selected-double-click', array($this, 'onSelectDate'));
        
        $this->month = new TCombo('tdate-month');
        $this->month->addItems(array(TAdiantiCoreTranslator::translate('January'),TAdiantiCoreTranslator::translate('February'),TAdiantiCoreTranslator::translate('March'),TAdiantiCoreTranslator::translate('April'),TAdiantiCoreTranslator::translate('May'),TAdiantiCoreTranslator::translate('June'),TAdiantiCoreTranslator::translate('July'),TAdiantiCoreTranslator::translate('August'),TAdiantiCoreTranslator::translate('September'),TAdiantiCoreTranslator::translate('October'),TAdiantiCoreTranslator::translate('November'),TAdiantiCoreTranslator::translate('December')));
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
        
        $bt_today = new GtkButton(TAdiantiCoreTranslator::translate('Today'));
        $bt_close = new GtkButton(TAdiantiCoreTranslator::translate('Close'));
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
        $this->entry->set_text($return);
        
        // fecha janela do calendário
        $this->popWindow->hide();
    }
    
    /**
     * Define the widget's name 
     * @param $name Widget's Name
     */
    public function setName($name)
    {
        $this->wname = $name;
    }
    
    /**
     * Returns the name of the widget
     */
    public function getName()
    {
        return $this->wname;
    }
    
    /**
     * Define the current date
     * @param $value A string containing a date
     */
    public function setValue($value)
    {
        $this->entry->set_text($value);
    }
    
    /**
     * Returns the current date
     */
    public function getValue()
    {
        return $this->entry->get_text();
    }
    
    /**
     * Define if the widget is editable
     * @param $boolean  A boolean indicating if the widget is editable
     */
    public function setEditable($editable)
    {
        $this->entry->set_sensitive($editable);
    }
    
    /**
     * Define the widget's size
     * @param $size Widget's size in pixels
     */
    public function setSize($size)
    {
        $this->entry->set_size_request($size, 24);
    }
    
    /**
     * Define the field's mask
     * @param $mask  Mask for the field (dd-mm-yyyy)
     */
    public function setMask($mask)
    {
        $this->mask = $mask;
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
    
    /**
     * Add a field validator
     * @param $validator TFieldValidator object
     */
    public function addValidation($label, TFieldValidator $validator, $parameters = NULL)
    {
        $this->validations[] = array($label, $validator, $parameters);
    }
    
    /**
     * Validate a field
     * @param $validator TFieldValidator object
     */
    public function validate()
    {
        if ($this->validations)
        {
            foreach ($this->validations as $validation)
            {
                $label      = $validation[0];
                $validator  = $validation[1];
                $parameters = $validation[2];
                
                $validator->validate($label, $this->getValue(), $parameters);
            }
        }
    }
}
?>