<?php
/**
 * Text Widget (also known as Memo)
 *
 * @version    1.0
 * @package    widget_gtk
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TText extends GtkScrolledWindow
{
    private $wname;
    private $validations;
    
    /**
     * Class Constructor
     * @param $name Widet's name
     */
    public function __construct($name)
    {
        parent::__construct();
        parent::set_size_request(200, -1);
        $this->textview = new GtkTextView;
        $this->textview->set_wrap_mode(Gtk::WRAP_WORD);
        $this->textbuffer = $this->textview->get_buffer();
        parent::add($this->textview);
        $this->wname = $name;
        $this->validations = array();
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
        parent::set_size_request($width, $height);
    }
    
    /**
     * Define if the widget is editable
     * @param $boolean  A boolean indicating if the widget is editable
     */
    public function setEditable($editable)
    {
        parent::set_sensitive($editable);
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