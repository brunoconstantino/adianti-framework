<?php
/**
 * Entry Widget (also known as Edit, Input)
 *
 * @version    1.0
 * @package    widget_gtk
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2012 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TEntry extends GtkEntry
{
    private $wname;
    private $mask;
    private $chars;
    private $handler;
    private $validations;
    
    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct($name)
    {
        $this->wname = $name;
        $this->chars = array('-', '_', '.', '/', '\\', ':',
                             '|', '(', ')', '[', ']', '{', '}');
        
        $this->validations = array();
        parent::__construct();
        parent::set_size_request(200, 24);
        
        // Connecting 'changed' signal to check the typed chars.
        $this->handler = parent::connect_after('changed', array($this, 'onChanged'));
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
        parent::set_text($value);
    }

    /**
     * Return the widget's content
     */
    public function getValue()
    {
        return parent::get_text();
    }
    
    /**
     * Define the widget's size
     * @param $size Widget's size in pixels
     */
    public function setSize($size)
    {
        $this->set_size_request($size, 24);
    }
    
    /**
     * Define if the widget is editable
     * @param $boolean A boolean
     */
    public function setEditable($editable)
    {
        parent::set_sensitive($editable);
    }
    
    /**
     * Define max length
     * @param  $length Max length
     */
    public function setMaxLength($length)
    {
        parent::set_max_length($length);
    }
    
    /**
     * Define widget properties
     * @param  $property Property's name
     * @param  $value    Property's value
     */
    public function setProperty($property, $value)
    {
        if ($property=='readonly')
        {
            parent::set_editable(false);
        }
    }
    
    /**
     * Define the field's mask
     * @param $mask A mask for input data
     */
    public function setMask($mask)
    {
        parent::set_max_length(strlen(trim($mask)));
        $this->mask = $mask;
    }
    
    /**
     * Changes the Entry contents without fire 'changed' signal
     * @param string $text the new text
     * @ignore-autocomplete on
     */
    public function Set($text)
    {
        // turn off the signal
        parent::disconnect($this->handler);
        parent::set_text($text);
        
        // cursor to the end
        parent::select_region(-1,-1);
        // turn on the signal
        $this->handler = parent::connect_after('changed', array($this, 'onChanged'));
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
            $text = parent::get_text();
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
        $text = parent::get_text();
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
        parent::set_completion($completion);
    }
}
?>