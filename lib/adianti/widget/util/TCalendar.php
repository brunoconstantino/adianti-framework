<?php
Namespace Adianti\Widget\Util;

use Adianti\Core\AdiantiCoreApplication;
use Adianti\Control\TAction;

use Gtk;
use GtkCalendar;

/**
 * Calendar Widget
 *
 * @version    2.0
 * @package    widget
 * @subpackage util
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TCalendar extends GtkCalendar
{
    private $year;
    private $month;
    private $action;
    
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct();
        parent::set_size_request(400,300);
        parent::connect_simple('day-selected-double-click', array($this, 'onSelectDate'));
    }
    
    /**
     * Define the calendar's size
     * @param  $width  Window's width
     * @param  $height Window's height
     */
    public function setSize($width, $height)
    {
        parent::set_size_request($width, $height);
    }
    
    /**
     * Define the current month to display
     * @param  $month Month to display
     */
    public function setMonth($month)
    {
        $date = $this->get_date();
        $year = $date[0];
        parent::select_month($month -1, $year);
    }
    
    /**
     * Define the current year to display
     * @param  $year Year to display
     */
    public function setYear($year)
    {
        $date = $this->get_date();
        $month = $date[1];
        parent::select_month($month, $year);
    }
    
    /**
     * Return the current month
     */
    public function getMonth()
    {
        $date = $this->get_date();
        return $date[1] +1;
    }
    
    /**
     * Return the current year
     */
    public function getYear()
    {
        $date = $this->get_date();
        return $date[0];
    }
    
    /**
     * Define the action when click at some day
     * @param  $action TAction object
     */
    public function setAction(TAction $action)
    {
        $this->action = $action;
        
    }
    
    /**
     * Executed when the user selects a date
     */
    public function onSelectDate()
    {
        if ($this->action instanceof TAction)
        {
            $date  = $this->get_date();
            $day   = $date[2];
            $month = $date[1] +1;
            $year  = $date[0];
            
            $callb = $this->action->getAction();
            $parameters = $this->action->getParameters();
            $parameters['year']  = $year;
            $parameters['month'] = $month;
            $parameters['day']   = $day;
            
            if (is_object($callb[0]))
            {
                $object = $callb[0];
                call_user_func($callb, $parameters);
            }
            else
            {
                $class  = $callb[0];
                $method = $callb[1];
                AdiantiCoreApplication::executeMethod($class, $method, $parameters);
            }
        }
    }
    
    /**
     * Select a collection of days
     * @param  $days Collection of days
     */
    public function selectDays(array $days)
    {
        foreach ($days as $day)
        {
            parent::select_day($day);
        }
    }
}
